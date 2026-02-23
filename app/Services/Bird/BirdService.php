<?php

namespace App\Services\Bird;

use Illuminate\Validation\ValidationException;
use App\Repository\Batch\BatchRepositoryInterface;
use App\Repository\Culling\CullingRepositoryInterface;
use App\Repository\Mortality\MortalityRepositoryInterface;

class BirdService implements BirdServiceInterface
{

     private $batchRepository;
     private $mortalityRepository;
     private $cullingRepository;

     public function __construct(BatchRepositoryInterface $batchRepository, MortalityRepositoryInterface $mortalityRepository, CullingRepositoryInterface $cullingRepository)
     {
         $this->batchRepository = $batchRepository;
         $this->mortalityRepository = $mortalityRepository;
         $this->cullingRepository = $cullingRepository;
     }

    public function applyMortality(array $data)
    {
        $batch = $this->batchRepository->find($data['batch_id']);

        if( $data['count'] > $batch->current_quantity) {
            throw ValidationException::withMessages(['exceeds' => 'Mortality exceeds live birds']);
        }

        $batch->current_quantity -= $data['count'];
        $batch->save();

        return $this->mortalityRepository->create($data);

    }

    public function applyCulling(array $data)
    {
        $batch = $this->batchRepository->find($data['batch_id']);

        if( $data['count'] > $batch->current_quantity) {
            throw ValidationException::withMessages(['exceeds' => 'Culling exceeds live birds']);
        }

        $batch->current_quantity -= $data['count'];
        $batch->save();

        return $this->cullingRepository->create($data);
    }
}
