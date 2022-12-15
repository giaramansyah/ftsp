<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    use HasFactory;

    protected $table = 'ms_year';

    protected $appends = ['years'];

    protected $fillable = [
        'year',
        'is_trash',
        'created_by',
        'updated_by',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(config('global.dateformat.view'));
    }

    public function getYearsAttribute()
    {
        return $this->year . '/' . ($this->year+1);
    }
}
