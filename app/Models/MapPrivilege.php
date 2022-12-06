<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapPrivilege extends Model
{
    use HasFactory;

    protected $table = 'map_privilege';

    protected $primaryKey = null;

    public $timestamps = false;
    
    public $incrementing = false;

    protected $fillable = [
        'privilege_group_id',
        'privilege_id',
    ];
}
