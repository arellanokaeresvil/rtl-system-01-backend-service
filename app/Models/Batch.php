<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Batch extends Model
{
    use SoftDeletes, HasUuids;
    protected $table = 'batches';

    protected $fillable = [
        'batch_code',
        'breed',
        'supplier_name',
        'date_received',
        'initial_age_weeks',
        'initial_quantity',
        'current_quantity',
        'cost_per_head',
        'daily_feed_per_bird_kg',
        'status',
        'notes'
    ];

    public function getCurrentAgeWeeksAttribute()
    {
        $weeks = $this->initial_age_weeks 
            + Carbon::parse($this->date_received)->diffInWeeks(Carbon::now());

        return (int) round($weeks);
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
}
