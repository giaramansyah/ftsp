<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'ts_expense';
    
    protected $appends = ['ma_id', 'staff', 'status', 'expense_date_format', 'apply_date_format', 'reff_date_format'];

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
        'image',
        'type',
        'created_by',
        'updated_by',
    ];

    public function datas()
    {
        return $this->hasOne(Data::class, 'id', 'data_id');
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
    
    public function getMaIdAttribute()
    {
        if(isset($this->id)) {
            $data = self::find($this->id)->datas()->select('ma_id')->first();
            return $data->ma_id;
        }
        return null;
    }

    public function getStaffAttribute()
    {
        if(isset($this->attributes['staff_id']) && $this->attributes['staff_id'] != 0) {
            $arrStaff = array_combine(config('global.staff.code'), config('global.staff.desc'));
            return $arrStaff[$this->attributes['staff_id']];
        }
        return '';
    }

    public function getStatusAttribute()
    {
        if(isset($this->attributes['type']) && $this->attributes['type'] != 0) {
            $arrType = array_combine(config('global.type.code'), config('global.type.status'));
            return $arrType[$this->attributes['type']];
        }
        return '';
    }

    public function getExpenseDateFormatAttribute()
    {
        if(isset($this->attributes['expense_date'])) {
            $date = Carbon::createFromFormat('Y-m-d', $this->attributes['expense_date']);
            return $date->format('d M Y');
        }
        return '';
    }

    public function getReffDateFormatAttribute()
    {
        if(isset($this->attributes['reff_date'])) {
            $date = Carbon::createFromFormat('Y-m-d', $this->attributes['reff_date']);
            return $date->format('d M Y');
        }
        return '';
    }

    public function getApplyDateFormatAttribute()
    {
        if(isset($this->attributes['apply_date'])) {
            $date = Carbon::createFromFormat('Y-m-d', $this->attributes['apply_date']);
            return $date->format('d M Y');
        }
        return '';
    }

}
