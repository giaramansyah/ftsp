<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $table = 'ts_offer';

    protected $appends = ['int_amount',];

    protected $fillable = [
        'offer_no',
        'letter_no',
        'date_offer',
        'date_letter',
        'date_apply',
        'ma_id',
        'changed_ma_id',
        'type_id',
        'name',
        'staff_id',
        'description',
        'subdescription',
        'amount',
        'text_amount',
        'account_number',
        'file_responsibility',
        'file_acceptence',
        'created_by',
        'updated_by',
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

    public function getIntAmountAttribute()
    {
        return $this->attributes['amount'];
    }
}
