<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'ts_expense';
    
    protected $appends = [];

    protected $fillable = [
        'expense_id',
        'expense_date',
        'reff_no',
        'reff_date',
        'description',
        'sub_description',
        'data_id',
        'name',
        'staff_id',
        'amount',
        'text_amount',
        'account',
        'apply_date',
        'file',
        'status',
        'type',
        'created_by',
        'updated_by',
    ];

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = preg_replace("/[^0-9.]/", "", $value);
    }

    public function getAmountAttribute()
    {
        return number_format($this->attributes['amount'], 0, null, ',');
    }


}
