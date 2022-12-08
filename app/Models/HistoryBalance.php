<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryBalance extends Model
{
    use HasFactory;

    protected $table = 'ts_history_balance';
    
    protected $appends = ['transaction'];

    protected $fillable = [
        'balance_id',
        'amount',
        'description',
        'transaction_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(config('global.dateformat.view'));
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = preg_replace("/[^0-9.]/", "", $value);
    }

    public function getAmountAttribute()
    {
        return number_format($this->attributes['amount'], 0, null, ',');
    }

    public function getTransactionAttribute()
    {
        if(isset($this->attributes['transaction_id']) && $this->attributes['transaction_id'] != 0) {
            $arrTransaction = array_combine(config('global.transaction.code'), config('global.transaction.desc'));
            return $arrTransaction[$this->attributes['transaction_id']];
        }
        return '';
    }

    
}
