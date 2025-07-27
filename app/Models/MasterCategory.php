<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterCategory extends Model
{
    protected $table = 'master_categories';

    protected $fillable = [
        'name',
        'name_ar',
        'description',
        'image',
    ];
}
