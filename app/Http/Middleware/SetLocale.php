<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;
class SetLocale {
    public function handle(Request $request, Closure $next): Response {
        App::setLocale(in_array($request->session()->get('locale'), ['en', 'mr'], true) ? $request->session()->get('locale') : 'en');
        return $next($request);
    }
}
