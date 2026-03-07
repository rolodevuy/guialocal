<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlugRedirect extends Model
{
    protected $fillable = ['old_slug', 'lugar_id'];

    public function lugar()
    {
        return $this->belongsTo(Lugar::class);
    }
}
