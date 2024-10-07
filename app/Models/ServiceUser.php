<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ServiceUser extends Pivot
{
    use HasFactory;

    protected $table = 'service_user';
    protected $fillable = [
        'user_id',
        'service_id',
    ];
}
