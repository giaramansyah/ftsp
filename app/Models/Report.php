<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = 'ts_report';
    
    protected $appends = ['division', 'years', 'report_date_format'];

    protected $fillable = [
        'year',
        'division_id',
        'report_date',
        'type',
        'knowing',
        'created_by',
        'updated_by',
    ];

    public function map()
    {
        return $this->hasMany(MapReport::class, 'id', 'report_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(config('global.dateformat.view'));
    }

    public function getReportDateFormatAttribute()
    {
        if(isset($this->attributes['report_date'])) {
            $date = Carbon::createFromFormat('Y-m-d', $this->attributes['report_date']);
            return $date->format('d F y');
        }
        return '';
    }

    public function getYearsAttribute()
    {
        if(isset($this->attributes['year'])) {
            return $this->attributes['year'] . '/' . ($this->attributes['year']+1);
        }
    }

    public function getDivisionAttribute()
    {
        if(isset($this->attributes['division_id']) && $this->attributes['division_id'] != 0) {
            $arrDivision = array_combine(config('global.compact_division.code'), config('global.compact_division.desc'));
            return $arrDivision[$this->attributes['division_id']];
        }
        return '';
    }
}
