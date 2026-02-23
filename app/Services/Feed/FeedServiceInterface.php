<?php 

namespace App\Services\Feed;

use App\Models\Feed;
use App\Models\FeedUsage;

interface FeedServiceInterface
{
    public function create(array $data): Feed;
    public function deduct(array $data): FeedUsage;
    public function getByType(): array;

}
