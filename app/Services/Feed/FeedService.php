<?php 

namespace App\Services\Feed;

use App\Models\ExpenseCategory;
use App\Models\Feed;
use App\Models\FeedUsage;
use App\Models\FeedAdjustment;
use App\Repository\Batch\BatchRepositoryInterface;
use App\Repository\Expense\ExpenseRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Repository\Feed\FeedRepositoryInterface;
use App\Repository\Feed\FeedUsageRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
        DB::beginTransaction();
        try {
            $requestedKg = floatval($data['quantity_kg']);
            $initialFeed = $this->feedRepository->find($data['feed_id']);

            if (!$initialFeed) {
                throw ValidationException::withMessages(['not_found' => 'Feed not found']);
            }

            // gather all available feeds of the same type (initial first)
            $otherFeeds = Feed::where('type', $initialFeed->type)
                ->where('remaining_kg', '>', 0)
                ->where('id', '!=', $initialFeed->id)
                ->orderBy('date_manufactured')
                ->get();

            $feedsList = collect([$initialFeed])->merge($otherFeeds);

            $totalAvailable = $feedsList->sum(fn($f) => floatval($f->remaining_kg));

            if ($totalAvailable < $requestedKg) {
                throw ValidationException::withMessages(['quantity_kg' => 'Insufficient total feed quantity for this feed type']);
            }

            $batch = $this->batchRepository->find($data['batch_id']);
            $expense_category = ExpenseCategory::where('name', 'Feeds')->orWhere('name', 'Feed')->first();

            $feedCodes = [];
            $totalExpenseAmount = 0;
            $lastUsage = null;

            foreach ($feedsList as $feed) {
                if ($requestedKg <= 0) break;

                $available = floatval($feed->remaining_kg);
                if ($available <= 0) continue;

                $deduct = min($available, $requestedKg);

                // update feed remaining
                $feed->remaining_kg = $available - $deduct;
                $feed->save();

                // create a feed usage record for this portion
                $usageData = $data;
                $usageData['feed_id'] = $feed->id;
                $usageData['quantity_kg'] = $deduct;

                $lastUsage = $this->feedUsageRepository->create($usageData);

                // accumulate expense amount (use each feed's cost_per_kg)
                $totalExpenseAmount += ($feed->cost_per_kg * $deduct);
                $feedCodes[] = $feed->feed_code;

                $requestedKg -= $deduct;
            }

            // create single expense record summarizing the whole consumption
            $expense = [
                'expense_category_id' => $expense_category?->id,
                'expense_date' => $data['used_at'],
                'amount' => $totalExpenseAmount,
                'reference_no' => implode(', ', $feedCodes),
                'description' => "System created: {$data['quantity_kg']}kg feeds consumed by {$batch->batch_code}"
            ];

            $this->expenseRepository->create($expense);

            DB::commit();

            // return last created usage record (keeps signature). You can adjust to return summary if desired.
            return $lastUsage;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
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
            ->map(function($feed) {
                // Calculate total daily consumption across all active batches
                $totalDailyConsumption = \App\Models\Batch::where('status', '!=','sold')
                ->orWhere('status','!=','culled')
                    ->get()
                    ->sum(function($batch) {
                        return ( ($batch->current_quantity * $batch->daily_feed_per_bird_kg) / 1000);
                    });

                // Calculate expected days before stock runs out
                $expectedDaysToConsume = $totalDailyConsumption > 0 
                    ? round(floatval($feed->total_quantity_kg) / $totalDailyConsumption)
                    : 0;

                return array_merge($feed->toArray(), [
                    'expected_days_to_consume' => $expectedDaysToConsume,
                    'daily_consumption_kg' => round($totalDailyConsumption, 2)
                ]);
            })
            ->toArray();

        return $feeds;
    }


    public function reconcile($id, array $data)
    {
        try {
            DB::beginTransaction();

                $feed = $this->feedRepository->find($id);
                if (!$feed) {
                    throw ValidationException::withMessages(['not_found' => 'Feed not found']);
                }

                // Calculate the difference between the new remaining quantity and the current remaining quantity
                $difference = floatval($data['quantity_kg']) - floatval($feed->remaining_kg);

                // Update the feed's remaining quantity
                $feed->remaining_kg = floatval($data['quantity_kg']);
                $feed->save();

        

                // Create a feed adjustment record
                $adjustmentData = [
                    'feed_id' => $feed->id,
                    'quantity_kg' => abs($difference),
                    'cost' => abs($difference) * floatval($feed->cost_per_kg),
                    'reason' => $data['reason'],
                    'remarks' => $data['remarks'] ?? null,
                ];

                $expense_category = ExpenseCategory::where('name', 'Inventory Adjustment')->first();

                $expenseData = [
                    'expense_category_id' => $expense_category?->id,
                    'expense_date' => now(),
                    'amount' => abs($difference) * floatval($feed->cost_per_kg),
                    'reference_no' => "Adjustment for feed: {$feed->feed_code}",
                    'description' => "System created: Adjustment of {$difference}kg for feed {$feed->feed_code}. Reason: {$data['reason']}"
                ];

                $this->expenseRepository->create($expenseData);

                $data = FeedAdjustment::create($adjustmentData);

        
            DB::commit();
            return $data;
           

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
       
    }



}
