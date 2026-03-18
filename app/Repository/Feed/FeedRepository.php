<?php

namespace App\Repository\Feed;

use App\Models\Feed;
use App\Repository\Base\BaseRepository;

class FeedRepository extends BaseRepository implements FeedRepositoryInterface
{
    protected $model;

    public function __construct(Feed $model)
    {
        $this->model = $model;
    }

    public function Options()
    {
        return $this->model->where('remaining_kg', '>', 0)->orderBy('created_at', 'ASC')->get();
    }
}