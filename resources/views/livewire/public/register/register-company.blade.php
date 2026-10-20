<div>
    <div class="mb-8">
        <a href="{{ route('register.choice') }}" wire:navigate class="inline-flex items-center gap-1 text-sm text-[#2c6904] hover:underline font-medium mb-3">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Types de compte
        </a>
        <h2 class="font-sora text-3xl font-bold text-[#1e1b18]">
            Inscription {{ $entity_type === 'centre_formation' ? 'centre de formation' : 'entreprise' }}
        </h2>
        <p class="mt-2 text-[#41493b]">Compte activé après validation par un opérateur.</p>
    </div>

    <form wire:submit="submit" class="space-y-5">
        <div>
            <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Raison sociale</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-xl">apartment</span>
                <input type="text" wire:model="name" placeholder="Nom de l'organisation"
                       class="w-full pl-10 pr-4 py-3 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
            </div>
            @error('name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Prénom du représentant</label>
                <input type="text" wire:model="legal_rep_first_name"
                       class="w-full px-4 py-3 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                @error('legal_rep_first_name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Nom du représentant</label>
                <input type="text" wire:model="legal_rep_last_name"
                       class="w-full px-4 py-3 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                @error('legal_rep_last_name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Adresse e-mail</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-xl">mail</span>
                <input type="email" wire:model="email" placeholder="votre@email.bf"
                       class="w-full pl-10 pr-4 py-3 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
            </div>
            @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Téléphone</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-xl">call</span>
                <input type="text" wire:model="phone" placeholder="+226 XX XX XX XX"
                       class="w-full pl-10 pr-4 py-3 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
            </div>
            @error('phone') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Mot de passe</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-xl">lock</span>
                <input type="password" wire:model="password" placeholder="••••••••"
                       class="w-full pl-10 pr-4 py-3 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
            </div>
            @error('password') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-[#1e1b18] mb-1.5">Confirmer le mot de passe</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-xl">lock</span>
                <input type="password" wire:model="password_confirmation" placeholder="••••••••"
                       class="w-full pl-10 pr-4 py-3 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
            </div>
        </div>

        <button type="submit"
                class="w-full bg-[#2c6904] hover:bg-[#448322] text-white font-semibold py-3.5 rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-[#2c6904]/20">
            <span class="material-symbols-outlined">domain_add</span>
            Soumettre l'inscription
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-[#41493b]">
        Déjà un compte ?
        <a href="{{ route('login') }}" class="text-[#2c6904] font-semibold hover:underline">Se connecter</a>
    </p>
</div>
