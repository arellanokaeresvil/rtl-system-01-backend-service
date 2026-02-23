<?php 

namespace App\Services\Batch;

use App\Models\Batch;

interface BatchServiceInterface
{
    public function create(array $data): Batch;
    // public function update(Batch $batch, array $data): Batch;
    // public function delete(Batch $batch): bool;
}
