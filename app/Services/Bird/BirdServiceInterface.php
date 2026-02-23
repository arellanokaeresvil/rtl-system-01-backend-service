<?php 

namespace App\Services\Bird;

interface BirdServiceInterface
{
    public function applyCulling(array $birdIds);
    public function applyMortality(array $birdIds);
}
