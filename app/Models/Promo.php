<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mvdnbrk\EloquentExpirable\Expirable;
use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Promo extends Model
{
    use HasFactory, HasUUID, SoftDeletes, Expirable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'value',
        'venue',
        'radius',
        'expires_at'
    ];

    public static function generate()
    {
        return 'Safeboda' . Str::random(6);
    }

    public static function fetch_ttl($datetime)
    {
        return now()->diffInSeconds($datetime);
    }
}
