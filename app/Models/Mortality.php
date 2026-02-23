<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Testing\Fluent\Concerns\Has;

class Mortality extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'mortalities';

    protected $fillable = [
        'batch_id',
        'date',
        'count',
        'cause',
        'notes',
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
}
