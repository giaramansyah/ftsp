<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'ms_menus';

    public function parent()
    {
        return $this->belongsTo(ParentMenu::class, 'parent_id');
    }
}
