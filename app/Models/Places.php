<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Places extends Model
{
    use HasFactory;

    protected $table = 'places';

    protected $fillable = ['id', 'title', 'slug', 'area', 'introduce', 'country', 'created_at', 'updated_at'];
}