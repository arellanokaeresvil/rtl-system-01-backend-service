<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedUsage extends Model
{
    use HasUuids, SoftDeletes;
    protected $table = 'feed_usages';

    protected $fillable = [
        'feed_id',
        'batch_id',
        'used_at',
        'quantity_kg',
        'source',
        'usage_type',
        'remarks'
    ];

    public function feed()
    {
        return $this->belongsTo(Feed::class);
    }

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
