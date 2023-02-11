<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $table = 'ms_note';

    protected $appends = ['staff', 'staff_id', 'staff_export', 'years', 'division', 'status_desc', 'note_date_format', 'note_upload_format'];

    protected $fillable = [
        'year',
        'division_id',
        'ma_id',
        'note_reff',
        'note_date',
        'note_upload',
        'program',
        'regarding',
        'link_url',
        'amount',
        'amount_requested',
        'amount_approved',
        'status',
        'is_trash',
        'created_by',
        'updated_by',
    ];

    public function staffs()
    {
        return $this->hasMany(MapNote::class, 'note_id', 'id');
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

    public function setAmountApprovedAttribute($value)
    {
        $this->attributes['amount_approved'] = preg_replace("/[^0-9.]/", "", $value);
    }

    public function getAmountApprovedAttribute()
    {
        return number_format($this->attributes['amount_approved'], 0, null, ',');
    }

    public function setAmountRequestedAttribute($value)
    {
        $this->attributes['amount_requested'] = preg_replace("/[^0-9.]/", "", $value);
    }

    public function getAmountRequestedAttribute()
    {
        return number_format($this->attributes['amount_requested'], 0, null, ',');
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
            $arrDivision = array_combine(config('global.division.code'), config('global.division.desc'));
            return $arrDivision[$this->attributes['division_id']];
        }
        return '';
    }

    public function getStaffIdAttribute()
    {
        if(isset($this->attributes['id'])) {
            $staffs = self::find($this->id)->staffs()->get()->toArray();
            return array_column($staffs, 'staff_id');
        }
        return array();
    }

    public function getStaffAttribute()
    {
        if(isset($this->attributes['id'])) {
            $staffs = self::find($this->id)->staffs()->get()->toArray();
            $arrStaff = array_combine(config('global.staff.code'), config('global.staff.desc'));
            $descStaff = array();
            foreach($staffs as $staff) {
                if(isset($staff['staff_id']) && $staff['staff_id'] != 0) {
                    $descStaff[] = $arrStaff[$staff['staff_id']];
                }
            }

            return implode(', ', $descStaff);
        }
        return '';
    }

    public function getStaffExportAttribute()
    {
        if(isset($this->attributes['id'])) {
            $staffs = self::find($this->id)->staffs()->get()->toArray();
            $arrStaff = array_combine(config('global.staff.code'), config('global.staff.export'));
            $descStaff = array();
            foreach($staffs as $staff) {
                if(isset($staff['staff_id']) && $staff['staff_id'] != 0) {
                    $descStaff[] = $arrStaff[$staff['staff_id']];
                }
            }

            return implode(', ', $descStaff);
        }
        return '';
    }

    public function getStatusDescAttribute()
    {   
        if(isset($this->attributes['status'])) {
            $arrStatus = array_combine(config('global.status.code'), config('global.status.desc'));
            return $arrStatus[$this->attributes['status']];
        }

        return '';
    }

    public function getNoteDateFormatAttribute()
    {
        if(isset($this->attributes['note_date'])) {
            $date = Carbon::createFromFormat('Y-m-d', $this->attributes['note_date']);
            return $date->format('d M Y');
        }
        return '';
    }

    public function getNoteUploadFormatAttribute()
    {
        if(isset($this->attributes['note_upload'])) {
            $date = Carbon::createFromFormat('Y-m-d', $this->attributes['note_upload']);
            return $date->format('d M Y');
        }
        return '';
    }
}
