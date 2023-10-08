<?php

namespace App\Http\Middleware;

use App\Models\File;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FileAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $file = File::where('id',$request->route('id'))->first();
        $user_id = Auth::user()->id ?? -1;
        if($file->access === 'PRIVATE' & $user_id != $file->user_id) {
            return response()->json(['error'=>'Доступ запрещён']);
        }
        return $next($request);
    }
}
