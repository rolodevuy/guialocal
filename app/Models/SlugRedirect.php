<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlugRedirect extends Model
{
    protected $fillable = ['old_slug', 'negocio_id'];

    public function negocio()
    {
        return $this->belongsTo(Negocio::class);
    }
}
