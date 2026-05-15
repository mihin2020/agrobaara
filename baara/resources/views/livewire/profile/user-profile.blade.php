<div class="space-y-6">

    {{-- En-tête --}}
    <div>
        <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Mon profil</h2>
        <p class="text-[#41493b] mt-1 text-sm">Gérez vos informations personnelles et votre mot de passe.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Colonne principale --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Carte — Informations personnelles --}}
            <div class="bg-white rounded-2xl border border-[#c1c9b6] p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-[#aef585]/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-base text-[#2c6904]">person</span>
                    </div>
                    <h3 class="font-sora text-base font-bold text-[#1e1b18]">Informations personnelles</h3>
                </div>

                @if($profileSaved)
                    <div class="mb-4 flex items-center gap-2 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-800 font-medium">
                        <span class="material-symbols-outlined text-base text-green-600">check_circle</span>
                        Profil mis à jour avec succès.
                    </div>
                @endif

                <form wire:submit="saveProfile" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Prénom</label>
                            <input wire:model="first_name" type="text"
                                   class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all @error('first_name') border-red-400 @enderror"
                                   placeholder="Prénom" />
                            @error('first_name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Nom</label>
                            <input wire:model="last_name" type="text"
                                   class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all @error('last_name') border-red-400 @enderror"
                                   placeholder="Nom" />
                            @error('last_name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Adresse e-mail</label>
                        <input wire:model="email" type="email"
                               class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all @error('email') border-red-400 @enderror"
                               placeholder="adresse@exemple.com" />
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-60 cursor-not-allowed"
                                class="flex items-center gap-2 px-5 py-2.5 bg-[#2c6904] text-white text-sm font-semibold rounded-xl hover:bg-[#448322] transition-colors">
                            <span wire:loading.remove wire:target="saveProfile" class="material-symbols-outlined text-base">save</span>
                            <span wire:loading wire:target="saveProfile" class="material-symbols-outlined text-base animate-spin">progress_activity</span>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>

            {{-- Carte — Changer le mot de passe --}}
            <div id="securite" class="bg-white rounded-2xl border border-[#c1c9b6] p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                        <span class="material-symbols-outlined text-base text-amber-600">lock</span>
                    </div>
                    <h3 class="font-sora text-base font-bold text-[#1e1b18]">Sécurité — Mot de passe</h3>
                </div>

                @if($passwordSaved)
                    <div class="mb-4 flex items-center gap-2 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-800 font-medium">
                        <span class="material-symbols-outlined text-base text-green-600">check_circle</span>
                        Mot de passe modifié avec succès.
                    </div>
                @endif

                <form wire:submit="changePassword" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Mot de passe actuel</label>
                        <input wire:model="current_password" type="password"
                               class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all @error('current_password') border-red-400 @enderror"
                               placeholder="••••••••" autocomplete="current-password" />
                        @error('current_password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Nouveau mot de passe</label>
                            <input wire:model="new_password" type="password"
                                   class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all @error('new_password') border-red-400 @enderror"
                                   placeholder="••••••••" autocomplete="new-password" />
                            @error('new_password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Confirmer le mot de passe</label>
                            <input wire:model="new_password_confirmation" type="password"
                                   class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all"
                                   placeholder="••••••••" autocomplete="new-password" />
                        </div>
                    </div>

                    <div class="bg-[#fbf2ed] rounded-xl p-4 text-xs text-[#41493b] space-y-1">
                        <p class="font-semibold text-[#1e1b18] mb-1.5">Règles du mot de passe :</p>
                        <div class="flex items-center gap-1.5"><span class="material-symbols-outlined text-sm text-[#717a69]">fiber_manual_record</span>Au moins 8 caractères</div>
                        <div class="flex items-center gap-1.5"><span class="material-symbols-outlined text-sm text-[#717a69]">fiber_manual_record</span>Lettres majuscules et minuscules</div>
                        <div class="flex items-center gap-1.5"><span class="material-symbols-outlined text-sm text-[#717a69]">fiber_manual_record</span>Au moins un chiffre</div>
                        <div class="flex items-center gap-1.5"><span class="material-symbols-outlined text-sm text-[#717a69]">fiber_manual_record</span>Au moins un caractère spécial</div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-60 cursor-not-allowed"
                                class="flex items-center gap-2 px-5 py-2.5 bg-amber-600 text-white text-sm font-semibold rounded-xl hover:bg-amber-700 transition-colors">
                            <span wire:loading.remove wire:target="changePassword" class="material-symbols-outlined text-base">lock_reset</span>
                            <span wire:loading wire:target="changePassword" class="material-symbols-outlined text-base animate-spin">progress_activity</span>
                            Modifier le mot de passe
                        </button>
                    </div>
                </form>
            </div>

        </div>

        {{-- Colonne latérale — Infos compte --}}
        <div class="space-y-5">

            {{-- Avatar --}}
            <div class="bg-white rounded-2xl border border-[#c1c9b6] p-6 flex flex-col items-center text-center">
                <div class="w-20 h-20 rounded-full bg-[#2c6904] flex items-center justify-center text-white font-bold text-2xl overflow-hidden border-4 border-[#aef585]/60 mb-4">
                    @if($user->avatar)
                        <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-full h-full object-cover" />
                    @else
                        {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                    @endif
                </div>
                <p class="font-sora font-bold text-base text-[#1e1b18]">{{ $user->full_name }}</p>
                <p class="text-xs text-[#717a69] mt-1">{{ $user->email }}</p>
                <div class="mt-3">
                    @foreach($user->roles as $role)
                        <span class="inline-flex items-center px-3 py-1 bg-[#aef585]/30 text-[#2c6904] text-xs font-bold rounded-full">
                            {{ $role->name }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Infos connexion --}}
            <div class="bg-white rounded-2xl border border-[#c1c9b6] p-5 space-y-3">
                <h4 class="text-xs font-bold text-[#717a69] uppercase tracking-widest mb-3">Informations du compte</h4>

                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-base text-[#717a69] mt-0.5">schedule</span>
                    <div>
                        <p class="text-xs text-[#717a69]">Dernière connexion</p>
                        <p class="text-sm font-medium text-[#1e1b18]">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Inconnue' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-base text-[#717a69] mt-0.5">verified_user</span>
                    <div>
                        <p class="text-xs text-[#717a69]">Statut du compte</p>
                        <p class="text-sm font-medium text-green-700">Actif</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-base text-[#717a69] mt-0.5">calendar_today</span>
                    <div>
                        <p class="text-xs text-[#717a69]">Membre depuis</p>
                        <p class="text-sm font-medium text-[#1e1b18]">
                            {{ $user->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>