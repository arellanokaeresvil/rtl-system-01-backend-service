<?php

namespace App\Services\Egg;

use App\Models\Egg;
use App\Services\Egg\EggServiceInterface;

class EggService implements EggServiceInterface
{
    public function getByGrade(): array
    {
         $eggs = Egg::select('grade')
            ->whereDate('date_collected', now()->toDateString())
            ->selectRaw('COUNT(*) as count')
            ->groupBy('grade')
            ->get()
            ->toArray();

        return $eggs;
    }

    public function getByBatch(array $search = [])
    {
        $eggs = Egg::select('batch_id', 'date_collected')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN grade = "J" THEN 1 ELSE 0 END) as jumbo')
            ->selectRaw('SUM(CASE WHEN grade = "XL" THEN 1 ELSE 0 END) as extra_large')
            ->selectRaw('SUM(CASE WHEN grade = "L" THEN 1 ELSE 0 END) as large')
            ->selectRaw('SUM(CASE WHEN grade = "M" THEN 1 ELSE 0 END) as medium')
            ->selectRaw('SUM(CASE WHEN grade = "S" THEN 1 ELSE 0 END) as small')
            ->selectRaw('SUM(CASE WHEN grade = "XS" THEN 1 ELSE 0 END) as extra_small')
            ->selectRaw('SUM(CASE WHEN grade = "P" THEN 1 ELSE 0 END) as pewee')
            ->groupBy('batch_id', 'date_collected')
            ->filter($search)
           ->orderBy('date_collected', 'desc')->paginate(request('limit') ?? 10);

        return $eggs;
    }
}
