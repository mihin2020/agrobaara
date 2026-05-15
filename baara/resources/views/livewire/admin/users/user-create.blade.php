<div class="space-y-6 max-w-2xl mx-auto">

    {{-- En-tête --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.admin.users.index') }}" wire:navigate
           class="p-2 text-[#41493b] hover:bg-[#f5ece7] rounded-xl transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Nouvel Utilisateur</h2>
            <p class="text-[#41493b] text-sm mt-0.5">Créer un compte d'accès au back-office</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center gap-2">
            <span class="material-symbols-outlined text-base text-[#1e1b18]">manage_accounts</span>
            <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Informations du compte</h3>
        </div>
        <div class="p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form.input wire:model="first_name" label="Prénom *" placeholder="Ex: Fatoumata" :error="$errors->first('first_name')" />
                <x-form.input wire:model="last_name" label="Nom *" placeholder="Ex: SAWADOGO" :error="$errors->first('last_name')" />
                <div class="md:col-span-2">
                    <x-form.input wire:model="email" label="Adresse e-mail *" type="email" placeholder="Ex: f.sawadogo@agroecobaara.bf" :error="$errors->first('email')" />
                </div>
                <div class="md:col-span-2">
                    <x-form.select wire:model="role" label="Rôle *" :error="$errors->first('role')">
                        <option value="">Sélectionner un rôle...</option>
                        @foreach($roles as $r)
                            <option value="{{ $r['value'] }}">{{ $r['label'] }}</option>
                        @endforeach
                    </x-form.select>
                </div>
            </div>

            {{-- Notice invitation --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                <span class="material-symbols-outlined text-blue-600 text-lg flex-shrink-0 mt-0.5">mail</span>
                <div>
                    <p class="font-semibold text-sm text-blue-900">Invitation par e-mail</p>
                    <p class="text-xs text-blue-700 mt-1">
                        L'utilisateur recevra un e-mail avec un lien pour définir lui-même son mot de passe.
                        Le lien est valable <strong>60 minutes</strong>.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3 pb-4">
        <a href="{{ route('admin.admin.users.index') }}" wire:navigate
           class="px-5 py-2.5 border border-[#c1c9b6] text-[#41493b] font-semibold rounded-xl hover:bg-[#f5ece7] transition-colors text-sm">
            Annuler
        </a>
        <button wire:click="save" type="button"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-75"
                class="flex items-center gap-2 px-6 py-2.5 bg-[#1e1b18] text-white font-bold rounded-xl hover:opacity-90 transition-opacity text-sm">
            <span wire:loading.remove class="material-symbols-outlined text-base">send</span>
            <span wire:loading class="material-symbols-outlined animate-spin text-base">progress_activity</span>
            <span wire:loading.remove>Créer & Envoyer l'invitation</span>
            <span wire:loading>Envoi en cours...</span>
        </button>
    </div>
</div>