<?php 

namespace App\Services\Egg;

interface EggServiceInterface
{
    public function getByGrade(): array;
    public function getByBatch();

}
