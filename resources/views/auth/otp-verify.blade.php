<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Verificar codigo | Formulas Medicas</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100">
        <main class="relative mx-auto flex min-h-screen w-full items-center justify-center overflow-hidden px-6 py-10">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute -left-16 top-10 h-80 w-80 rounded-full bg-cyan-400/20 blur-3xl"></div>
                <div class="absolute -right-20 bottom-0 h-96 w-96 rounded-full bg-emerald-400/20 blur-3xl"></div>
                <div class="absolute left-1/2 top-1/3 h-72 w-72 -translate-x-1/2 rounded-full bg-sky-300/10 blur-3xl"></div>
            </div>

            <section class="relative w-full max-w-xl overflow-hidden rounded-3xl border border-white/10 bg-slate-900/80 p-8 shadow-2xl backdrop-blur sm:p-10">
                <div class="mx-auto max-w-md">
                    <div class="inline-flex items-center gap-3 rounded-full border border-cyan-300/30 bg-cyan-300/10 px-4 py-2 text-xs font-medium text-cyan-200">
                        Segundo factor de autenticacion
                    </div>

                    <h1 class="mt-5 text-3xl font-semibold text-white">Verificacion de codigo</h1>
                    <p class="mt-2 text-sm leading-6 text-slate-300">
                        Enviamos un codigo de 6 digitos a <strong>{{ $email }}</strong>.
                    </p>

                    @if(session('status'))
                        <div class="mt-4 rounded-xl border border-emerald-300/30 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form class="mt-6 space-y-4" action="{{ route('otp.verify.post') }}" method="POST">
                        @csrf
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-200" for="code">Codigo OTP</label>
                            <input id="code" name="code" type="text" required autofocus maxlength="6" inputmode="numeric"
                                class="w-full rounded-2xl border border-slate-700 bg-slate-950/80 px-4 py-3 text-sm tracking-[0.3em] text-white placeholder:text-slate-500 focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-400/30 @error('code') border-red-400 focus:border-red-400 focus:ring-red-400/20 @enderror"
                                placeholder="000000">
                            @error('code')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-cyan-500 to-emerald-500 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:from-cyan-400 hover:to-emerald-400">
                            Verificar codigo
                        </button>
                    </form>

                    <form class="mt-3" action="{{ route('otp.resend') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm font-semibold text-cyan-300 hover:text-cyan-200 hover:underline">
                            Reenviar codigo
                        </button>
                    </form>

                    <p class="mt-6 text-center text-sm text-slate-300">
                        <a href="{{ route('login') }}" class="font-semibold text-cyan-300 hover:text-cyan-200 hover:underline">Volver al inicio de sesion</a>
                    </p>
                </div>
            </section>
        </main>
    </body>
</html>
