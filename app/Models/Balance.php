<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    use HasFactory;

    protected $table = 'ms_balance';
    
    protected $appends = ['division'];

    protected $fillable = [
        'division_id',
        'amount',
        'is_trash',
        'created_by',
        'updated_by',
    ];

    public function histories()
    {
        return $this->hasMany(HistoryBalance::class, 'balance_id', 'id');
    }

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

    public function getDivisionAttribute()
    {
        if(isset($this->attributes['division_id']) && $this->attributes['division_id'] != 0) {
            $arrDivision = array_combine(config('global.division.code'), config('global.division.desc'));
            return $arrDivision[$this->attributes['division_id']];
        }
        return '';
    }


}
