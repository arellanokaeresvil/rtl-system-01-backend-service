<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Egg extends Model
{
   use SoftDeletes, HasUuids;
    protected $table = 'eggs';

    protected $fillable = [
        'batch_id',
        'date_collected',
        'weight_grams',
        'grade',
        'status',
        'source',
        'device_id',
        'unit',
        'total',
        'remaining'
    ];

     public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function scopeFilter($query, $filters)
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

    protected static function booted()
    {
        static::saving(function ($egg) {
            if ($egg->weight_grams) {
                $egg->grade = match (true) {
                    $egg->weight_grams <= 45 => 'P', // Pewee
                    $egg->weight_grams <= 50 => 'XS', // XS
                    $egg->weight_grams <= 55 => 'S', // S
                    $egg->weight_grams <= 60 => 'M', // Medium
                    $egg->weight_grams <= 65 => 'L', // Large
                    $egg->weight_grams <= 70 => 'XL', // Extra Large
                    default => 'J', // Jumbo
                };
            }
        });
    }


    
}
