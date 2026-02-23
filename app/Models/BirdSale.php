<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BirdSale extends Model
{
    use HasUuids, SoftDeletes; 
    protected $table = 'bird_sales';
    protected $fillable =[
            'batch_id',
            'sold_to',
            'sold_at',
            'count',
            'price_per_bird',
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
