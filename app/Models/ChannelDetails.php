<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'channel_name',
        'access_token',
        'expires_in',
        'refresh_token',
        'scope',
        'token_type',
    ];
}
