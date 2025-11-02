<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MergeHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'primary_id',
        'secondary_id',
        'details',
        'merged_at',
    ];
}
