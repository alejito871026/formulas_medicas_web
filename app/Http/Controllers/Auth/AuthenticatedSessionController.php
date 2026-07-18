<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Services\ResendEmailService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function __construct(private readonly ResendEmailService $resendEmailService)
    {
    }

    public function create(): Response
    {
        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Credenciales incorrectas.',
            ]);
        }

        $authUser = Auth::user();

        if ($authUser && $authUser->activo === false) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Tu cuenta esta desactivada. Contacta al administrador.',
            ]);
        }

        if (! $authUser instanceof User) {
            throw ValidationException::withMessages([
                'email' => 'No fue posible iniciar sesion.',
            ]);
        }

        $remember = $request->boolean('remember');
        $otp = $authUser->generateOtp();

        $this->resendEmailService->send(
            $authUser->email,
            'Codigo de verificacion',
            View::make('emails.otp', [
                'otp' => $otp,
                'user' => $authUser,
            ])->render(),
            "Tu codigo de verificacion es: {$otp}. Este codigo es valido por 2 minutos."
        );

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->put('otp_user_id', $authUser->id);
        $request->session()->put('otp_remember', $remember);

        return redirect()->route('otp.verify');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
