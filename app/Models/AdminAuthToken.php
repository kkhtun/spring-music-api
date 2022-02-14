<?php

namespace App\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminAuthToken extends Model
{
    use HasFactory;

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
