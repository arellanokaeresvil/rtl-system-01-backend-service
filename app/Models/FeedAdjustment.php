<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedAdjustment extends Model
{
    use HasUuids, SoftDeletes;
    protected $table = 'feed_adjustment';

    protected $fillable = [
        'feed_id',
        'quantity_kg',
        'cost',
        'reason',
        'remarks',
    ];

    public function feed()
    {
        return $this->belongsTo(Feed::class);
    }


}
