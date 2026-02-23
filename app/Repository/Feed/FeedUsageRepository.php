<?php

namespace App\Repository\Feed;

use App\Models\FeedUsage;
use App\Repository\Base\BaseRepository;

class FeedUsageRepository extends BaseRepository implements FeedUsageRepositoryInterface
{
    protected $model;

    public function __construct(FeedUsage $model)
    {
        $this->model = $model;
    }
}