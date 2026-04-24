<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

class UserPreferencesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Set Locale
            if ($user->language) {
                App::setLocale($user->language);
            }
            
            // Share theme with all views
            View::share('userTheme', $user->theme ?? 'light');
        } else {
            // Default for guests
            View::share('userTheme', 'light');
        }

        return $next($request);
    }
}
