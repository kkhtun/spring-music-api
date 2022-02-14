<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AdminAuthToken;

class Admin extends Model
{
    use HasFactory;

    public function admin_token()
    {
        return $this->hasOne(AdminAuthToken::class, 'admin_id');
    }
}
