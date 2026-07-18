<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ResendEmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class OtpVerificationController extends Controller
{
    public function __construct(private readonly ResendEmailService $resendEmailService)
    {
    }

    public function show(Request $request)
    {
        $user = $this->getOtpUserFromSession($request);

        if (! $user) {
            return redirect()->route('login');
        }

        if ($this->otpExpired($user)) {
            return $this->redirectToLoginAfterExpiredOtp($request, $user);
        }

        return view('auth.otp-verify', ['email' => $user->email]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $this->getOtpUserFromSession($request);

        if (! $user) {
            return redirect()->route('login');
        }

        if ($this->otpExpired($user)) {
            return $this->redirectToLoginAfterExpiredOtp($request, $user);
        }

        if (! $user->verifyOtp($request->string('code')->toString())) {
            return back()->withErrors([
                'code' => 'El codigo ingresado no es valido.',
            ]);
        }

        $remember = (bool) $request->session()->pull('otp_remember', false);

        $user->clearOtp();

        $request->session()->forget('otp_user_id');
        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = $this->getOtpUserFromSession($request);

        if (! $user) {
            return redirect()->route('login');
        }

        $otp = $user->generateOtp();

        $this->resendEmailService->send(
            $user->email,
            'Nuevo codigo de verificacion',
            View::make('emails.otp', [
                'otp' => $otp,
                'user' => $user,
            ])->render(),
            "Tu nuevo codigo de verificacion es: {$otp}. Este codigo es valido por 2 minutos."
        );

        return back()->with('status', 'Se ha enviado un nuevo codigo a tu correo.');
    }

    private function getOtpUserFromSession(Request $request): ?User
    {
        $userId = $request->session()->get('otp_user_id');

        if (! is_numeric($userId)) {
            return null;
        }

        return User::find((int) $userId);
    }

    private function otpExpired(User $user): bool
    {
        return $user->otp_expires_at !== null && now()->gt($user->otp_expires_at);
    }

    private function redirectToLoginAfterExpiredOtp(Request $request, User $user): RedirectResponse
    {
        $user->clearOtp();
        $request->session()->forget(['otp_user_id', 'otp_remember']);

        return redirect()->route('login')->withErrors([
            'email' => 'El codigo OTP expiro. Inicia sesion nuevamente.',
        ]);
    }
}
