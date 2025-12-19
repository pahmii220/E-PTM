<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetRequest extends Model
{
    protected $fillable = [
        'username',
        'status',
        'approved_at'
    ];
}

