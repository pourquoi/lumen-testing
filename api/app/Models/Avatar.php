<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avatar extends Model
{
    protected $attributes = [
        'priority' => 0,
    ];

    protected $fillable = [
        'path',
        'priority'
    ];

    protected $hidden = [
        'path'
    ];

    protected $appends = [
        'url'
    ];

    public function getUrlAttribute()
    {
        return env('APP_URL') . '/uploads' . $this->path;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
