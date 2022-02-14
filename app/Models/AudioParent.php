<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioParent extends Model
{
    use HasFactory;

    public function audios()
    {
        return $this->belongsToMany(Audio::class, 'audio_parent_audio');
    }
}
