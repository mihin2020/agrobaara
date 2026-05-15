<div>
    @if ($submitted)
        {{-- État succès --}}
        <div class="flex flex-col items-center justify-center h-full py-12 text-center gap-6">
            <div class="w-20 h-20 rounded-full bg-primary-container flex items-center justify-center">
                <span class="material-symbols-outlined text-primary" style="font-size:40px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 48">check_circle</span>
            </div>
            <h3 class="font-headline-md text-headline-md text-on-surface">Message envoyé !</h3>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-sm">
                Merci pour votre message. Notre équipe vous répondra sous 48h ouvrées.
            </p>
            <button wire:click="$set('submitted', false)"
                    class="font-label-bold text-label-bold text-primary flex items-center gap-2 hover:gap-4 transition-all">
                Envoyer un autre message <span class="material-symbols-outlined">east</span>
            </button>
        </div>
    @else
        <form wire:submit.prevent="submit" class="space-y-6">

            {{-- Honeypot anti-spam --}}
            <div style="display:none" aria-hidden="true">
                <input type="text" wire:model="honeypot" tabindex="-1" autocomplete="off" />
            </div>

            {{-- Nom + Email --}}
            <div class="grid sm:grid-cols-2 gap-6">
                <div>
                    <label class="font-label-bold text-label-bold text-on-surface-variant block mb-2">
                        Nom complet <span class="text-error">*</span>
                    </label>
                    <input wire:model="full_name" type="text" placeholder="Ex : Jean Traoré"
                           class="w-full bg-surface-container-low border border-outline-variant rounded-xl p-4
                                  font-body-md text-body-md text-on-surface
                                  focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all
                                  @error('full_name') border-error @enderror" />
                    @error('full_name')
                        <p class="text-error font-body-sm text-body-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="font-label-bold text-label-bold text-on-surface-variant block mb-2">
                        Email <span class="text-error">*</span>
                    </label>
                    <input wire:model="email" type="email" placeholder="jean@exemple.bf"
                           class="w-full bg-surface-container-low border border-outline-variant rounded-xl p-4
                                  font-body-md text-body-md text-on-surface
                                  focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all
                                  @error('email') border-error @enderror" />
                    @error('email')
                        <p class="text-error font-body-sm text-body-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Téléphone --}}
            <div>
                <label class="font-label-bold text-label-bold text-on-surface-variant block mb-2">
                    Téléphone
                </label>
                <input wire:model="phone" type="tel" placeholder="+226 XX XX XX XX"
                       class="w-full bg-surface-container-low border border-outline-variant rounded-xl p-4
                              font-body-md text-body-md text-on-surface
                              focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" />
            </div>

            {{-- Message --}}
            <div>
                <label class="font-label-bold text-label-bold text-on-surface-variant block mb-2">
                    Message <span class="text-error">*</span>
                </label>
                <textarea wire:model="message" rows="4"
                          placeholder="Dites-nous comment nous pouvons vous aider…"
                          class="w-full bg-surface-container-low border border-outline-variant rounded-xl p-4
                                 font-body-md text-body-md text-on-surface
                                 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none
                                 @error('message') border-error @enderror"></textarea>
                @error('message')
                    <p class="text-error font-body-sm text-body-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- RGPD --}}
            <div class="flex items-start gap-3">
                <input wire:model="rgpd_consent" type="checkbox" id="rgpd"
                       class="mt-0.5 w-4 h-4 rounded border-outline-variant text-primary
                              focus:ring-primary/20 cursor-pointer flex-shrink-0" />
                <label for="rgpd" class="font-body-sm text-body-sm text-on-surface-variant cursor-pointer leading-5">
                    J'accepte que mes données soient utilisées pour traiter ma demande.
                </label>
            </div>
            @error('rgpd_consent')
                <p class="text-error font-body-sm text-body-sm -mt-4">{{ $message }}</p>
            @enderror

            {{-- Bouton --}}
            <button type="submit"
                    class="w-full bg-primary hover:bg-primary-container text-on-primary
                           font-headline-sm text-headline-sm py-4 rounded-xl
                           shadow-lg transition-all flex items-center justify-center gap-2"
                    wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-wait">
                <span wire:loading.remove>Envoyer le message</span>
                <span wire:loading class="flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Envoi en cours…
                </span>
            </button>

        </form>
    @endif
</div>
