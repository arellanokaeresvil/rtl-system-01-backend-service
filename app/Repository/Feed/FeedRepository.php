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
       
        if(request('type')){
            $this->model = $this->model->where('type', request('type'));
        }

        return $this->model->where('remaining_kg', '>', 0)->orderBy('date_manufactured', 'ASC')->get();
    }
}