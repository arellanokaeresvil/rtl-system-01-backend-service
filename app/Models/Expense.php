<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Expense extends Model
{
    use HasUuids, SoftDeletes;
    protected $table = 'expenses';
    protected $fillable = [
        'batch_id',
        'expense_category_id',
        'expense_date',
        'amount',
        'reference_no',
        'description'
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
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
                            $query->orWhere($field, 'like', $search)
                             ->orWhereHas('category',function($query) use ($search){    
                                $query->where('name', 'like', $search);
                            })
                             ->orWhereHas('batch',function($query) use ($search){    
                                $query->where('batch_code', 'like', $search);
                                $query->where('supplier_name', 'like', $search);
                            });
                        }
                    });
                });
            }
        );

    }
}
