<?php

// app/Models/Token.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $fillable = ['token_number', 'mobile', 'status'];
}

