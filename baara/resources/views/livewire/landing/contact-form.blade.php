<div>
    @if($submitted)
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-20 h-20 rounded-full bg-[#2c6904]/10 flex items-center justify-center mb-6">
                <span class="material-symbols-outlined text-[#2c6904] text-5xl" style="font-variation-settings:'FILL' 1">check_circle</span>
            </div>
            <h3 class="font-sora text-2xl font-bold text-[#1e1b18] mb-3">Message envoyé !</h3>
            <p class="text-[#41493b] text-base mb-8">Merci pour votre message. Notre équipe vous répondra sous 48h ouvrées.</p>
            <button wire:click="$set('submitted', false)"
                    class="text-[#2c6904] font-semibold text-sm hover:underline flex items-center gap-1">
                <span class="material-symbols-outlined text-base">refresh</span>
                Envoyer un autre message
            </button>
        </div>
    @else
        <form wire:submit="submit" class="space-y-5">

            {{-- Honeypot anti-spam --}}
            <div style="display:none">
                <input wire:model="honeypot" type="text" name="website" tabindex="-1" autocomplete="off" />
            </div>

            {{-- Nom + Email --}}
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-[#41493b] mb-1.5">Nom complet <span class="text-red-500">*</span></label>
                    <input wire:model.blur="full_name"
                           type="text" placeholder="Ex: Jean Traoré"
                           class="w-full bg-[#fbf2ed] border @error('full_name') border-red-400 bg-red-50 @else border-[#c1c9b6] @enderror rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all" />
                    @error('full_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#41493b] mb-1.5">Email <span class="text-red-500">*</span></label>
                    <input wire:model.blur="email"
                           type="email" placeholder="jean@exemple.bf"
                           class="w-full bg-[#fbf2ed] border @error('email') border-red-400 bg-red-50 @else border-[#c1c9b6] @enderror rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all" />
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Téléphone + Profil --}}
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-[#41493b] mb-1.5">Téléphone</label>
                    <input wire:model.blur="phone"
                           type="tel" placeholder="+226 XX XX XX XX"
                           class="w-full bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#41493b] mb-1.5">Vous êtes <span class="text-red-500">*</span></label>
                    <select wire:model="profile"
                            class="w-full bg-[#fbf2ed] border @error('profile') border-red-400 @else border-[#c1c9b6] @enderror rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all">
                        <option value="">-- Sélectionnez --</option>
                        <option value="jeune">Un jeune cherchant un emploi</option>
                        <option value="entreprise">Une entreprise / ferme</option>
                        <option value="ong">Une ONG / partenaire</option>
                        <option value="autre">Autre</option>
                    </select>
                    @error('profile') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Objet --}}
            <div>
                <label class="block text-sm font-semibold text-[#41493b] mb-1.5">Objet <span class="text-red-500">*</span></label>
                <input wire:model.blur="subject"
                       type="text" placeholder="Ex: Demande d'information sur vos services"
                       class="w-full bg-[#fbf2ed] border @error('subject') border-red-400 bg-red-50 @else border-[#c1c9b6] @enderror rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all" />
                @error('subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Message --}}
            <div>
                <label class="block text-sm font-semibold text-[#41493b] mb-1.5">Message <span class="text-red-500">*</span></label>
                <textarea wire:model.blur="message"
                          rows="4" placeholder="Dites-nous comment nous pouvons vous aider..."
                          class="w-full bg-[#fbf2ed] border @error('message') border-red-400 bg-red-50 @else border-[#c1c9b6] @enderror rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all resize-none"></textarea>
                @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- RGPD --}}
            <div class="flex items-start gap-3">
                <input wire:model="rgpd_consent" id="rgpd" type="checkbox"
                       class="mt-0.5 w-4 h-4 text-[#2c6904] border-[#c1c9b6] rounded focus:ring-[#2c6904]/20" />
                <label for="rgpd" class="text-xs text-[#41493b] leading-relaxed">
                    J'accepte que mes données soient utilisées pour traiter ma demande conformément à la
                    <a href="#" class="text-[#2c6904] hover:underline">politique de confidentialité</a>. <span class="text-red-500">*</span>
                </label>
            </div>
            @error('rgpd_consent') <p class="text-xs text-red-600 -mt-3">{{ $message }}</p> @enderror

            {{-- Bouton --}}
            <button type="submit"
                    class="w-full bg-[#2c6904] hover:bg-[#448322] text-white font-sora font-semibold py-4 rounded-xl shadow-lg shadow-[#2c6904]/20 transition-all flex items-center justify-center gap-2"
                    wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-wait">
                <span wire:loading.remove class="material-symbols-outlined">send</span>
                <span wire:loading class="material-symbols-outlined animate-spin">progress_activity</span>
                <span wire:loading.remove>Envoyer le message</span>
                <span wire:loading>Envoi en cours...</span>
            </button>
        </form>
    @endif
</div>
