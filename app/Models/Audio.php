<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    use HasFactory;

    protected $table = 'audios';

    protected $fillable = ['title', 'art_work_file_path', 'audio_file_path',];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_audio', 'audio_id', 'category_id');
    }

    public function audioParents()
    {
        return $this->belongsToMany(AudioParent::class, 'audio_parent_audio', 'audio_id', 'audio_parent_id');
    }
}
