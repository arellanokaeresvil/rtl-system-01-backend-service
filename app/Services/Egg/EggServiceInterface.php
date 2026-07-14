<?php 

namespace App\Services\Egg;

interface EggServiceInterface
{
    public function getByGrade(): array;
    public function getByBatch();
    public function getAvailableEgg();
    public function storePerPiece(array $data);
    public function storePerTray(array $data);
    public function storeCustomize(array $data);
    public function loadProfitabilityEggs(): array;

}
