@php
    $feedbackStatus = null;
    $feedbackMessage = null;

    if (session('success')) {
        $feedbackStatus = 'success';
        $feedbackMessage = session('success');
    } elseif (session('error') || $errors->any()) {
        $feedbackStatus = 'error';
        $feedbackMessage = session('error') ?: 'Se presentaron errores de validacion. Revisa el formulario.';
    }
@endphp

<div id="submit-feedback-overlay" data-feedback-status="{{ $feedbackStatus }}" data-feedback-message="{{ $feedbackMessage }}" class="fixed inset-0 z-[80] hidden items-center justify-center bg-slate-950/60 px-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 text-center shadow-2xl ring-1 ring-slate-200">
        <div id="submit-feedback-spinner" class="mx-auto h-12 w-12 animate-spin rounded-full border-4 border-slate-200 border-t-teal-600"></div>
        <div id="submit-feedback-icon" class="mx-auto hidden h-12 w-12 items-center justify-center rounded-full text-2xl"></div>

        <h4 id="submit-feedback-title" class="mt-4 text-lg font-semibold text-slate-900">Guardando informacion</h4>
        <p id="submit-feedback-text" class="mt-2 text-sm text-slate-600">Por favor espera, estamos procesando tu solicitud.</p>

        <button id="submit-feedback-close" type="button" class="mt-5 hidden rounded-xl bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-900">
            Entendido
        </button>
    </div>
</div>

<script>
(function () {
    const overlay = document.getElementById('submit-feedback-overlay');
    const spinner = document.getElementById('submit-feedback-spinner');
    const icon = document.getElementById('submit-feedback-icon');
    const title = document.getElementById('submit-feedback-title');
    const text = document.getElementById('submit-feedback-text');
    const closeBtn = document.getElementById('submit-feedback-close');

    if (!overlay || !spinner || !icon || !title || !text || !closeBtn) {
        return;
    }

    const showLoading = () => {
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
        spinner.classList.remove('hidden');
        icon.classList.add('hidden');
        closeBtn.classList.add('hidden');
        title.textContent = 'Guardando informacion';
        text.textContent = 'Por favor espera, estamos procesando tu solicitud.';
    };

    const showResult = (status, message) => {
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
        spinner.classList.add('hidden');
        icon.classList.remove('hidden');
        closeBtn.classList.remove('hidden');

        if (status === 'success') {
            icon.className = 'mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-2xl text-emerald-600';
            icon.textContent = '✓';
            title.textContent = 'Operacion completada';
        } else {
            icon.className = 'mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-rose-100 text-2xl text-rose-600';
            icon.textContent = '!';
            title.textContent = 'No se pudo completar';
        }

        text.textContent = message;
    };

    document.querySelectorAll('form[data-feedback-form="true"]').forEach((form) => {
        form.addEventListener('submit', () => {
            showLoading();
        });
    });

    closeBtn.addEventListener('click', () => {
        overlay.classList.add('hidden');
        overlay.classList.remove('flex');
    });

    const status = overlay.dataset.feedbackStatus || '';
    const message = overlay.dataset.feedbackMessage || '';

    if (status && message) {
        showResult(status, message);
    }
})();
</script>
