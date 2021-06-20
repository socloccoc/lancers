<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LancersWorkDetail extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $hidden = [
        'work_id',
        'created_at',
        'updated_at',
    ];
}
