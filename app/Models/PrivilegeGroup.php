<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivilegeGroup extends Model
{
    use HasFactory;

    protected $table = 'ms_privilege_group';

    protected $fillable = [
        'name',
        'description',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function privileges()
    {
        return $this->belongsToMany(Privilege::class, 'map_privilege', 'privilege_group_id', 'privilege_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(config('global.dateformat.view'));
    }
}
