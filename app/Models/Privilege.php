<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Privilege extends Model
{
    use HasFactory;

    protected $table = 'ms_privilege';

    public $timestamps = false;

    protected $appends = ['menu_label', 'modules_name'];

    protected $fillable = [
        'code',
        'menu_id',
        'modules',
        'desc',
    ];

    protected $hidden = ['pivot'];

    public function menu()
    {
        return $this->hasOne(Menu::class, 'id', 'menu_id');
    }

    public function getModulesNameAttribute()
    {
        $modules = array_combine(config('global.modules.code'), config('global.modules.desc'));
        return __($modules[$this->attributes['modules']]);
    }

    public function getMenuLabelAttribute()
    {
        return __(self::where('code', $this->code)->first()->menu->label);
    }
}
