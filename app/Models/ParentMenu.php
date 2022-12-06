<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentMenu extends Model
{
    use HasFactory;

    protected $appends = ['menu'];

    protected $table = 'ms_parent_menus';

    public function menus()
    {
        return $this->hasMany(Menu::class, 'parent_id', 'id');
    }

    public function getMenuAttribute()
    {
        return self::find($this->id)->menus()->where('is_active', 1)->orderBy('order', 'asc')->get()->toArray();
    }
}
