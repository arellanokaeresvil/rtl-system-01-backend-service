<?php 

namespace App\Services\Batch;

use App\Models\Batch;
use Illuminate\Support\Facades\Log;
use App\Repository\Batch\BatchRepositoryInterface;

class BatchService implements BatchServiceInterface
{

    private $batchRepository;

    public function __construct(BatchRepositoryInterface $batchRepository)
    {
        $this->batchRepository = $batchRepository;
    }

    public function create(array $data): Batch
    {
        $countBatches = Batch::withTrashed()->count();
        $year = date('Y');
        $batch_code = 'RTL-' . $year . '-' . str_pad($countBatches + 1, 3, '0', STR_PAD_LEFT);
        $data['batch_code'] = $batch_code;
        return $this->batchRepository->create($data);
    }

}
