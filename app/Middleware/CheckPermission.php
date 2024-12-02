<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Fortify\Features;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        $user = auth()->user();

        // Check if user is not logged in
        if (!$user) {
            return redirect(route('login'));
        }

        // Check if user is not active
        if (!$user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            return redirect(route('login'))->withErrors(['permission' => 'Your account is not active']);
        }

        // Check if user does not have permission to access dashboard
        if (!$user->hasAnyPermission($permission) && $permission == "access_dashboard") {
            auth()->logout();
            $request->session()->invalidate();
            return redirect(route('login'))->withErrors(['permission' => 'You don\'t  have permission to access dashboard']);
        }

        if (Features::enabled(Features::twoFactorAuthentication())) {
            // Check if route is two factor authentication route
            $tfaRoute = in_array($request->url(), [route('two-factor.setup'), route('two-factor.finish')]);

            // Check if user didn't enable two factor authentication
            if (!$user->two_factor_confirmed_at && !$tfaRoute) {
                return redirect(route('two-factor.setup'));
            }

            // Check if user enabled two factor authentication
            if ($user->two_factor_confirmed_at && $tfaRoute) {
                return redirect(route('Dashboard'));
            }
        }

        // Check if user does not have a specific permission
        if (!$user->hasAnyPermission($permission)) {
            return redirect(route('Dashboard'))->with('error', ['permission' => 'Access Denied']);
        }

        return $next($request);
    }
}
