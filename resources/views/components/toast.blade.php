@php
    $flashToasts = [];
    $flashMap = [
        'success'        => 'success',
        'error'          => 'error',
        'warning'        => 'warning',
        'upload_success' => 'success',
        'upload_error'   => 'error',
    ];
    foreach ($flashMap as $key => $type) {
        if (session()->has($key)) {
            $flashToasts[] = [
                'id'      => 'flash-' . $key . '-' . uniqid(),
                'type'    => $type,
                'message' => (string) session($key),
            ];
        }
    }
@endphp

<div
    x-data="toastStack(@js($flashToasts))"
    x-on:notify.window="add(typeof $event.detail === 'object' && $event.detail !== null && !Array.isArray($event.detail) ? $event.detail : ($event.detail?.[0] ?? $event.detail))"
    class="fixed top-4 inset-x-4 sm:inset-x-auto sm:right-4 z-[10000] flex flex-col gap-2 w-auto sm:w-96 max-w-full pointer-events-none"
    role="region"
    aria-label="Notifications"
    aria-live="polite"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2 sm:translate-x-4"
            x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0 translate-y-1"
            class="pointer-events-auto flex items-start gap-3 rounded-xl border px-4 py-3 shadow-lg text-sm"
            :class="{
                'bg-green-50 border-green-200 text-green-800': toast.type === 'success',
                'bg-red-50 border-red-200 text-red-800': toast.type === 'error',
                'bg-amber-50 border-amber-200 text-amber-900': toast.type === 'warning',
                'bg-white border-[#c1c9b6] text-[#1e1b18]': !['success','error','warning'].includes(toast.type),
            }"
        >
            <span class="material-symbols-outlined text-base flex-shrink-0 mt-0.5"
                  x-text="toast.type === 'error' ? 'error' : (toast.type === 'warning' ? 'warning' : 'check_circle')"></span>
            <p class="flex-1 font-medium leading-snug" x-text="toast.message"></p>
            <button type="button"
                    @click="dismiss(toast.id)"
                    class="flex-shrink-0 p-0.5 rounded-lg hover:bg-black/5 transition-colors"
                    aria-label="Fermer">
                <span class="material-symbols-outlined text-base">close</span>
            </button>
        </div>
    </template>
</div>

<script>
    function toastStack(initial) {
        return {
            toasts: Array.isArray(initial) ? initial : [],
            _bound: false,
            init() {
                this.toasts.forEach((t) => this.scheduleDismiss(t.id));
                if (this._bound || typeof Livewire === 'undefined') {
                    return;
                }
                this._bound = true;
                Livewire.on('notify', (...args) => {
                    const payload = args[0];
                    const data = Array.isArray(payload) ? payload[0] : payload;
                    if (data && (data.message || data.type)) {
                        this.add(data);
                    }
                });
            },
            add(data) {
                if (!data || !data.message) {
                    return;
                }
                const id = 't-' + Date.now() + '-' + Math.random().toString(36).slice(2, 8);
                this.toasts.push({
                    id,
                    type: data.type || 'success',
                    message: String(data.message),
                });
                this.scheduleDismiss(id);
            },
            scheduleDismiss(id) {
                setTimeout(() => this.dismiss(id), 4000);
            },
            dismiss(id) {
                this.toasts = this.toasts.filter((t) => t.id !== id);
            },
        };
    }
</script>
