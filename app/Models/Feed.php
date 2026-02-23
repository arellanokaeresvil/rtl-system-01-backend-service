<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Feed extends Model
{
    use HasUuids, SoftDeletes;
    
    protected $table = 'feeds';

    protected $fillable = [
        'feed_code',
        'name',
        'type',
        'date_manufactured',
        'quantity_kg',
        'remaining_kg',
        'cost_per_kg',
        'supplier',
    ];

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

        // return data by type

        $query->when(
            request('type') ?? false,
            function ($query) {
                $query->where('type', request('type'));
            }
        );

    }
}
