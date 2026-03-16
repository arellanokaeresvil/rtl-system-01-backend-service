<?php 

namespace App\Services\Feed;

use App\Models\Feed;
use App\Models\FeedUsage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Repository\Feed\FeedRepositoryInterface;
use App\Repository\Feed\FeedUsageRepositoryInterface;

class FeedService implements FeedServiceInterface
{

    private $feedRepository;
    private $feedUsageRepository;

    public function __construct(FeedRepositoryInterface $feedRepository, FeedUsageRepositoryInterface $feedUsageRepository)
    {
        $this->feedRepository = $feedRepository;
        $this->feedUsageRepository = $feedUsageRepository;
    }
        public function create(array $data): Feed
    {
        $countFeeds = Feed::count();
        $date = date('Y-m-d');
        $feed_code = 'FD-' . $date . '-' . str_pad($countFeeds + 1, 3, '0', STR_PAD_LEFT);
        $data['feed_code'] = $feed_code;
        return $this->feedRepository->create($data);
    }

    public function deduct(array $data): FeedUsage
    {
        $feed = $this->feedRepository->find($data['feed_id']);
        if (!$feed) {
            throw ValidationException::withMessages(['not_found' => 'Feed not found']);
        }
        if ($feed->quantity_kg < $data['quantity_kg']) {
            throw ValidationException::withMessages(['quantity_kg' => 'Insufficient feed quantity']);
        }
        $feed->quantity_kg -= $data['quantity_kg'];
        $feed->save();

        return $this->feedUsageRepository->create($data);

    }

    public function getByType(): array
    {
            $feeds = Feed::select('type')
            ->selectRaw('SUM(quantity_kg) as total_quantity_kg')
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
