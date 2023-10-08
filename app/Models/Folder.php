<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;
    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];
    protected $fillable = ['name','user_id','path','folder_id'];

    public function files() {
        return $this->hasMany(File::class,'folder_id','id');
    }
    public function folders() {
        return $this->hasMany(Folder::class, 'folder_id', 'id');
    }
}
