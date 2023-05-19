<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User_Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','company_name' , 'position' , 'number', 'service' , 'email' ,'content'
    ];
}
