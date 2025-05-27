<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invite extends Model
{
    use HasFactory;
    
    protected $table = 'invites';
    protected $fillable = ['first_name', 'last_name', 'description', 'phone', 'presence'];
    protected $attributes = [
        'presence' => 'لم يتم التسجيل'
    ];
}
