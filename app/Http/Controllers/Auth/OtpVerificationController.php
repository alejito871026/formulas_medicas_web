<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ResendEmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        $expiresInSeconds = $user->otp_expires_at
            ? max(0, now()->diffInSeconds($user->otp_expires_at, false))
            : 0;

        return view('auth.otp-verify', [
            'email' => $user->email,
            'otpExpiresInSeconds' => $expiresInSeconds,
            'loginUrl' => route('login'),
        ]);
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

        if (app()->environment('testing')) {
            Log::channel('stderr')->info('OTP reenviado para pruebas', [
                'email' => $user->email,
                'otp' => $otp,
            ]);

            error_log(sprintf('OTP_TESTING_RESEND email=%s otp=%s', $user->email, $otp));
        }

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
