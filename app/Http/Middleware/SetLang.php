<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLang
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $lang = config('app.locale');

        if ($request->user()) {
            $lang = $request->user()->lang;
        }

        app()->setLocale($lang);

        return $next($request);
    }
}
