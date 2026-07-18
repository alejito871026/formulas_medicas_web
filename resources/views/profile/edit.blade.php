@extends('layouts.app', [
    'title' => 'Mi Perfil | Gestion de Formulas Medicas',
])

@section('module_nav')
    <div class="module-nav">
        <div>
            <p class="module-nav-title">Mi perfil</p>
            <p class="module-nav-subtitle">Actualiza datos de contacto y foto de perfil para tu cuenta.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Ir al dashboard</a>
    </div>
@endsection

@section('content')
    <article class="entity-form-card profile-page-card">
        <div class="entity-form-head">
            <h3 class="entity-form-title">Informacion de la cuenta</h3>
            <p class="entity-form-subtitle">Estos datos aplican al usuario autenticado, sin importar su rol.</p>
        </div>

        <div class="entity-form-body">
            @if (session('success'))
                <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <p class="font-semibold">No se pudo actualizar el perfil.</p>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="profile-edit-layout" data-feedback-form="true">
                @csrf
                @method('PUT')

                <aside class="profile-avatar-panel">
                    <div class="profile-avatar-shell">
                        @if ($user->avatar_url)
                            <img id="avatar-preview" src="{{ $user->avatar_url }}" alt="Avatar de perfil" class="profile-avatar-image">
                        @else
                            <div id="avatar-preview-fallback" class="profile-avatar-fallback">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</div>
                            <img id="avatar-preview" src="" alt="Avatar de perfil" class="profile-avatar-image hidden">
                        @endif
                    </div>

                    <p class="profile-avatar-name">{{ $user->name }}</p>
                    <p class="profile-avatar-role">{{ ucfirst($user->role?->nombre ?? 'sin rol') }}</p>

                    <label class="profile-avatar-upload">
                        <span>Cambiar avatar</span>
                        <input id="avatar-input" type="file" name="avatar" accept="image/*" class="hidden">
                    </label>

                    <label class="profile-avatar-remove">
                        <input type="checkbox" name="remove_avatar" value="1" @checked(old('remove_avatar') == '1')>
                        <span>Quitar avatar actual</span>
                    </label>

                    <p class="profile-avatar-help">JPG, PNG, GIF o WEBP. Maximo 2 MB.</p>
                </aside>

                <section class="profile-data-panel">
                    <div class="profile-data-section profile-data-wide">
                        <p class="profile-section-title">Datos basicos</p>

                        <div class="profile-field-grid">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Nombre</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="input-control w-full">
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Correo</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input-control w-full">
                            </div>
                        </div>
                    </div>

                    <div class="profile-data-section profile-data-wide">
                        <p class="profile-section-title">Contacto</p>

                        <div class="profile-field-grid">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Telefono</label>
                                <input type="text" name="telefono" value="{{ old('telefono', $user->telefono) }}" class="input-control w-full" maxlength="20">
                            </div>

                            <div class="profile-data-wide">
                                <label class="mb-1 block text-sm font-medium text-slate-700">Direccion</label>
                                <textarea name="direccion" rows="4" class="input-control w-full">{{ old('direccion', $user->direccion) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="entity-form-actions profile-data-wide">
                        <a href="{{ route('dashboard') }}" class="btn btn-muted">Cancelar</a>
                        <button type="submit" class="btn btn-teal">Guardar cambios</button>
                    </div>
                </section>
            </form>
        </div>
    </article>

    <script>
    (function () {
        const fileInput = document.getElementById('avatar-input');
        const preview = document.getElementById('avatar-preview');
        const fallback = document.getElementById('avatar-preview-fallback');

        if (!fileInput || !preview) {
            return;
        }

        fileInput.addEventListener('change', (event) => {
            const file = event.target.files && event.target.files[0];

            if (!file) {
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target?.result || '';
                preview.classList.remove('hidden');

                if (fallback) {
                    fallback.classList.add('hidden');
                }
            };

            reader.readAsDataURL(file);
        });
    })();
    </script>
@endsection
