<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioParent_Audio extends Model
{
    use HasFactory;
    protected $table = 'audio_parent_audio';
    public $timestamps = false;
}
