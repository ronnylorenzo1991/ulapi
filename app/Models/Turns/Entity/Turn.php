<?php

namespace App\Models\Turns\Entity;

use App\Models\TurnStatuses\Entity\TurnStatus;
use App\Models\Users\Entity\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turn extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'date',
        'time',
        'payment',
        'user_id',
        'status_id',
    ];

    protected $dates = [
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(TurnStatus::class);
    }
}
