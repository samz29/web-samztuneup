<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallFee extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'min_distance', 'max_distance', 'fee', 'active'];
}
