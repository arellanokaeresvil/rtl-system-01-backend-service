<?php 

namespace App\Services\Sale;

interface SaleServiceInterface
{
    public function summary(): array;
    public function records(): array;
    public function create($data, $type);
    public function updateStatus($data, $id, $type);
}
