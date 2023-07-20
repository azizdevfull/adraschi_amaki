<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecretPayment extends Model
{
    use HasFactory;

    protected $fillable = ['secret_code'];
}
