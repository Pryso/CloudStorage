<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessesToFile extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = false;
    public $incrementing = false;
}
