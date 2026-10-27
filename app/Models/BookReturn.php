<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookReturn extends Model
{
    protected $fillable = [
        'loan_id',
        'return_date',
        'condition',
        'status',
    ];
}
