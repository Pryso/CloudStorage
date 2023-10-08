<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class File extends Model
{
    use HasFactory;

    protected $fillable = ['path','url','name','user_id','extension','id','access','size','full_name','folder_id'];
    public $incrementing = false;
    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];
    public static function generateId(int $length = 20): string
    {

        $file_id = Str::random($length);

        $exists = File::where('id', $file_id)->get(['id']);

        if (isset($exists[0]->id)) {//id exists in users table
            return self::generateId();//Retry with another generated id
        }

        return $file_id;//Return the generated id as it does not exist in the DB
    }
    public static function userFiles() {
        return File::where('user_id',Auth::user()->id);
    }


}
