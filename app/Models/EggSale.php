<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EggSale extends Model
{
    use HasUuids, SoftDeletes; 
    protected $table = 'egg_sales';
    protected $fillable =[
            'batch_id',
            'sold_to',
            'sold_at',
            'quantity',
            'unit',
            'grade',
            'price_per_unit',
            'total_amount',
            'notes'

   ];

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }


    public function scopeFilter($query)
    {
        $search = request('search') ?? false;
        $query->when(
            request('search')  ?? false,
            function ($query) use ($search) {
                $search = '%' . $search . '%';
                $query->when($search, function ($query) use ($search) {
                    $search = '%' . $search . '%';
                    $fillableFields = $this->fillable;
                    $query->where(function ($query) use ($fillableFields, $search) {
                        foreach ($fillableFields as $field) {
                            $query->orWhere($field, 'like', $search);
                        }
                    });
                });
            }
        );

    }

}
