<?php 

namespace App\Services\Feed;

use App\Models\ExpenseCategory;
use App\Models\Feed;
use App\Models\FeedUsage;
use App\Repository\Batch\BatchRepositoryInterface;
use App\Repository\Expense\ExpenseRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Repository\Feed\FeedRepositoryInterface;
use App\Repository\Feed\FeedUsageRepositoryInterface;
use Illuminate\Support\Carbon;

class FeedService implements FeedServiceInterface
{

    private $feedRepository;
    private $batchRepository;
    private $expenseRepository;
    private $feedUsageRepository;

    public function __construct(
        FeedRepositoryInterface $feedRepository, 
        FeedUsageRepositoryInterface $feedUsageRepository, 
        BatchRepositoryInterface $batchRepository,
        ExpenseRepositoryInterface $expenseRepository
        )
    {
        $this->feedRepository = $feedRepository;
        $this->feedUsageRepository = $feedUsageRepository;
        $this->batchRepository = $batchRepository;
        $this->expenseRepository = $expenseRepository;
    }
        public function create(array $data): Feed
    {
        $countFeeds = Feed::count();
        $date = date('Y');
        $feed_code = 'FD-' . $date . '-' . str_pad($countFeeds + 1, 3, '0', STR_PAD_LEFT);
        $data['feed_code'] = $feed_code;
        $data['remaining_kg'] = $data['quantity_kg'];
        return $this->feedRepository->create($data);
    }

    public function deduct(array $data): FeedUsage
    {
        $feed = $this->feedRepository->find($data['feed_id']);
        $consumed_today = FeedUsage::where('batch_id', $data['batch_id'])->whereDate('used_at',$data['used_at'])->first();
        if ($consumed_today) {
            throw ValidationException::withMessages(['already_consumed' => 'The selected batch has already consumed feeds for the selected date.']);
        }
        if (!$feed) {
            throw ValidationException::withMessages(['not_found' => 'Feed not found']);
        }
        if ($feed->remaining_kg < $data['quantity_kg']) {
            throw ValidationException::withMessages(['quantity_kg' => 'Insufficient feed quantity']);
        }
        $feed->remaining_kg -= $data['quantity_kg'];
        $feed->save();

        $batch = $this->batchRepository->find($data['batch_id']);
        $expense_category = ExpenseCategory::where('name', 'Feeds')->first();
        $expense = [
            'expense_category_id' => $expense_category?->id,
            'expense_date' => $data['used_at'],
            'amount' => $feed->cost_per_kg * $data['quantity_kg'],
            'reference_no' => $feed->feed_code,
            'description' => "System created: {$data['quantity_kg']}kg feeds consumed by {$batch->batch_code}"
        ];

        $this->expenseRepository->create($expense);

        return $this->feedUsageRepository->create($data);

    }

    public function getByType(): array
    {
            $feeds = Feed::select('type')
            ->selectRaw('SUM(remaining_kg) as total_quantity_kg')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('MAX(date_manufactured) as last_restock')
            ->selectRaw('
            (SELECT supplier 
            FROM feeds f2 
            WHERE f2.type = feeds.type 
            ORDER BY created_at DESC 
            LIMIT 1) as supplier
        ')
            ->groupBy('type')
            ->get()
            ->toArray();

        return $feeds;
    }



}
