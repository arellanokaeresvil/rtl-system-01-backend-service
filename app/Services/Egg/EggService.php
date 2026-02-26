<?php

namespace App\Services\Egg;

use App\Models\Egg;
use App\Services\Egg\EggServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

use function Illuminate\Log\log;

class EggService implements EggServiceInterface
{
    public function getByGrade(): array
    {
         $eggs = Egg::select('grade')
            ->whereDate('date_collected', Carbon::now()->format('Y-m-d'))
           ->selectRaw('SUM(CASE WHEN unit = "piece" THEN 1 WHEN unit = "tray" THEN total * 30 WHEN unit = "custom" THEN total ELSE 0 END) as count')
            ->groupBy('grade')
            ->get()
            ->map(fn($item) => [
                'grade' => $item->grade,
                'count' => (int)$item->count
            ])
            ->toArray();

        return $eggs;
    }

    public function getAvailableEgg(): array
    {
        $eggs = Egg::select('grade')
            ->where('is_sold', false)
            ->selectRaw('SUM(CASE WHEN unit = "piece" THEN 1 WHEN unit = "tray" THEN total * 30 WHEN unit = "custom" THEN total ELSE 0 END) as count')
            ->groupBy('grade')
            ->get()
            ->map(fn($item) => [
                'grade' => $item->grade,
                'count' => (int)$item->count
            ])
            ->toArray();

        return $eggs;
    }

    public function getByBatch(array $search = [])
    {
      $eggs = Egg::select('batch_id', 'date_collected')
        ->selectRaw('SUM(CASE WHEN unit = "piece" THEN 1 WHEN unit = "tray" THEN total * 30 WHEN unit = "custom" THEN total ELSE 0 END) as total')
        ->selectRaw('SUM(CASE WHEN grade = "J" AND unit = "piece" THEN 1 WHEN grade = "J" AND unit = "tray" THEN total * 30 WHEN grade = "J" AND unit = "custom" THEN total ELSE 0 END) as jumbo')
        ->selectRaw('SUM(CASE WHEN grade = "XL" AND unit = "piece" THEN 1 WHEN grade = "XL" AND unit = "tray" THEN total * 30 WHEN grade = "XL" AND unit = "custom" THEN total ELSE 0 END) as extra_large')
        ->selectRaw('SUM(CASE WHEN grade = "L" AND unit = "piece" THEN 1 WHEN grade = "L" AND unit = "tray" THEN total * 30 WHEN grade = "L" AND unit = "custom" THEN total ELSE 0 END) as large')
        ->selectRaw('SUM(CASE WHEN grade = "M" AND unit = "piece" THEN 1 WHEN grade = "M" AND unit = "tray" THEN total * 30 WHEN grade = "M" AND unit = "custom" THEN total ELSE 0 END) as medium')
        ->selectRaw('SUM(CASE WHEN grade = "S" AND unit = "piece" THEN 1 WHEN grade = "S" AND unit = "tray" THEN total * 30 WHEN grade = "S" AND unit = "custom" THEN total ELSE 0 END) as small')
        ->selectRaw('SUM(CASE WHEN grade = "XS" AND unit = "piece" THEN 1 WHEN grade = "XS" AND unit = "tray" THEN total * 30 WHEN grade = "XS" AND unit = "custom" THEN total ELSE 0 END) as extra_small')
        ->selectRaw('SUM(CASE WHEN grade = "P" AND unit = "piece" THEN 1 WHEN grade = "P" AND unit = "tray" THEN total * 30 WHEN grade = "P" AND unit = "custom" THEN total ELSE 0 END) as pewee')
        ->groupBy('batch_id', 'date_collected')
        ->filter($search)
        ->orderBy('date_collected', 'desc')
        ->paginate(request('limit') ?? 10);

        return $eggs;
    }

    public function storePerPiece(array $data)
    {
      try {
        DB::beginTransaction();
           $data = collect($data['items'])->map(function ($item) {
                if ($item['weight_grams']) {
                    $item['grade'] = match (true) {
                        $item['weight_grams'] <= 45 => 'P',
                        $item['weight_grams'] <= 50 => 'XS',
                        $item['weight_grams'] <= 55 => 'S',
                        $item['weight_grams'] <= 60 => 'M',
                        $item['weight_grams'] <= 65 => 'L',
                        $item['weight_grams'] <= 70 => 'XL',
                        default => 'J',
                    };
                }
                return [
                    'id' => Str::uuid(),
                    'batch_id' => $item['batch_id'],
                    'date_collected' => $item['date_collected'],
                    'weight_grams' => $item['weight_grams'],
                    'grade' => $item['grade'],
                    'unit' => 'piece',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();
           Log::info('Data to be inserted: ' . json_encode($data));
        Egg::insert($data);
        DB::commit();
      } catch (\Throwable $th) {
        DB::rollBack();
        Log::error('Error inserting data: ' . $th->getMessage());
         throw ValidationException::withMessages([
                'creation_error' => "Failed to create record: " . $th->getMessage()
            ]);
      }
    }

    public function storePerTray(array $data)
    {
      try {
        DB::beginTransaction();
           $data = collect($data['items'])->map(function ($item) {
                return [
                    'id' => Str::uuid(),
                    'batch_id' => $item['batch_id'],
                    'date_collected' => $item['date_collected'],
                    'grade' => $item['grade'],
                    'unit' => 'tray',
                    'total' => $item['total'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();
           Log::info('Data to be inserted: ' . json_encode($data));
        Egg::insert($data);
        DB::commit();
      } catch (\Throwable $th) {
        DB::rollBack();
        Log::error('Error inserting data: ' . $th->getMessage());
         throw ValidationException::withMessages([
                'creation_error' => "Failed to create record: " . $th->getMessage()
            ]);
      }
    }

    public function storeCustomize(array $data)
    {
        try {
            DB::beginTransaction();
            $data = collect($data['items'])->map(function ($item) {
                return [
                    'id' => Str::uuid(),
                    'batch_id' => $item['batch_id'],
                    'date_collected' => $item['date_collected'],
                    'grade' => $item['grade'],
                    'unit' => 'custom',
                    'total' => $item['total'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            Egg::insert($data);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error inserting data customize: ' . $th->getMessage());
             throw ValidationException::withMessages([
                'creation_error' => "Failed to create record: " . $th->getMessage()
            ]);
        }
    }
}
