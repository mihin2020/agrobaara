<div>

    {{-- ── TOP APP BAR ── --}}
    <header class="bg-[#fff8f5] border-b border-[#c1c9b6] sticky top-0 z-50" x-data="{ open: false }">
        <nav class="flex justify-between items-center w-full px-4 md:px-16 py-4 max-w-[1280px] mx-auto">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Agro Eco BAARA" class="h-12 w-auto object-contain" />
                <span class="font-sora text-xl font-bold text-[#2c6904] hidden lg:block">Agro Eco BAARA</span>
            </div>
            <div class="hidden md:flex items-center gap-8">
                <a href="#accueil" class="text-[#2c6904] border-b-2 border-[#2c6904] pb-1 font-semibold text-sm hover:text-[#448322] transition-colors">Accueil</a>
                <a href="#services" class="text-[#41493b] font-semibold text-sm hover:text-[#2c6904] transition-colors">Services</a>
                <a href="#partenaires" class="text-[#41493b] font-semibold text-sm hover:text-[#2c6904] transition-colors">Partenaires</a>
                <a href="#contact" class="text-[#41493b] font-semibold text-sm hover:text-[#2c6904] transition-colors">Contact</a>
                @auth
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-1.5 bg-[#2c6904] text-white px-5 py-2 rounded-lg font-semibold text-sm hover:bg-[#448322] transition-all">
                        <span class="material-symbols-outlined text-base">dashboard</span>
                        Mon espace
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="flex items-center gap-1.5 bg-[#2c6904] text-white px-5 py-2 rounded-lg font-semibold text-sm hover:bg-[#448322] transition-all">
                        <span class="material-symbols-outlined text-base">login</span>
                        Connexion
                    </a>
                @endauth
            </div>
            {{-- Mobile: always show the CTA button + hamburger --}}
            <div class="flex items-center gap-2 md:hidden">
                @auth
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-1 bg-[#2c6904] text-white px-3 py-2 rounded-lg font-semibold text-xs">
                        <span class="material-symbols-outlined text-sm">dashboard</span>
                        Mon espace
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="flex items-center gap-1 bg-[#2c6904] text-white px-3 py-2 rounded-lg font-semibold text-xs">
                        <span class="material-symbols-outlined text-sm">login</span>
                        Connexion
                    </a>
                @endauth
                <button @click="open = !open" class="text-[#2c6904] p-1">
                    <span class="material-symbols-outlined text-3xl" x-text="open ? 'close' : 'menu'">menu</span>
                </button>
            </div>
        </nav>
        <div x-show="open" x-cloak x-transition
             class="md:hidden bg-[#fff8f5] border-t border-[#c1c9b6] px-4 py-4 space-y-3">
            <a href="#accueil" @click="open=false" class="block text-[#41493b] font-semibold py-2 hover:text-[#2c6904]">Accueil</a>
            <a href="#services" @click="open=false" class="block text-[#41493b] font-semibold py-2 hover:text-[#2c6904]">Services</a>
            <a href="#partenaires" @click="open=false" class="block text-[#41493b] font-semibold py-2 hover:text-[#2c6904]">Partenaires</a>
            <a href="#contact" @click="open=false" class="block text-[#41493b] font-semibold py-2 hover:text-[#2c6904]">Contact</a>
        </div>
    </header>

    <main id="accueil">

        {{-- ── HERO ── --}}
        <section class="relative min-h-[80vh] flex items-center overflow-hidden">
            <div class="absolute inset-0 z-0">
                <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-transparent z-10"></div>
                <img src="{{ $hero['image_url'] ?? 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=1600&q=80' }}"
                     alt="Ferme agroécologique Burkina Faso"
                     class="w-full h-full object-cover" />
            </div>
            <div class="relative z-20 px-4 md:px-16 max-w-[1280px] mx-auto w-full text-white">
                <div class="max-w-2xl">
                    <h1 class="font-sora text-4xl md:text-5xl font-bold leading-tight mb-6" style="letter-spacing: -0.02em;">
                        {{ $hero['title'] ?? 'Connecter les talents et les opportunités en agroécologie' }}
                    </h1>
                    <p class="text-lg md:text-xl leading-relaxed mb-8 text-[#e9e1dc]">
                        {{ $hero['subtitle'] ?? 'Le guichet unique pour l\'insertion professionnelle et le recrutement spécialisé dans l\'agriculture durable au Burkina Faso.' }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ $hero['cta_primary_link'] ?? '#contact' }}"
                           class="bg-[#2c6904] hover:bg-[#448322] text-white px-8 py-4 rounded-xl font-sora font-semibold text-lg flex items-center justify-center gap-2 transition-all shadow-lg">
                            {{ $hero['cta_primary_text'] ?? 'Nous contacter' }}
                            <span class="material-symbols-outlined">arrow_forward</span>
                        </a>
                        <a href="{{ $hero['cta_secondary_link'] ?? '#services' }}"
                           class="bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20 text-white px-8 py-4 rounded-xl font-sora font-semibold text-lg flex items-center justify-center transition-all">
                            {{ $hero['cta_secondary_text'] ?? 'Découvrir nos services' }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── POUR QUI ── --}}
        @if($sectionVisibility['pour_qui'] ?? true)
        <section class="py-20 px-4 md:px-16 max-w-[1280px] mx-auto">
            <div class="text-center mb-16">
                <h2 class="font-sora text-3xl font-bold text-[#2c6904] mb-4">{{ $pourQui['title'] ?? 'Pour qui ?' }}</h2>
                <div class="h-1.5 w-24 bg-[#875212] mx-auto rounded-full"></div>
            </div>
            <div class="grid md:grid-cols-2 gap-8">
                @php $pqCards = $pourQui['cards'] ?? []; @endphp

                {{-- Carte Candidats --}}
                @php $c0 = $pqCards[0] ?? []; @endphp
                <div class="group relative overflow-hidden rounded-3xl bg-[#fbf2ed] border border-[#c1c9b6] p-10 hover:-translate-y-1 transition-transform duration-300">
                    <div class="mb-6 inline-flex p-4 rounded-2xl bg-[#448322] text-white">
                        <span class="material-symbols-outlined text-4xl">person_search</span>
                    </div>
                    <h3 class="font-sora text-2xl font-bold text-[#1e1b18] mb-4">{{ $c0['title'] ?? 'Jeunes & Candidats' }}</h3>
                    <p class="text-[#41493b] text-base leading-relaxed mb-6">{{ $c0['description'] ?? '' }}</p>
                    <ul class="space-y-3 mb-8 text-[#41493b] font-semibold text-sm">
                        @foreach($c0['list'] ?? ['Accès aux offres exclusives', 'Accompagnement personnalisé', 'Formations certifiantes'] as $item)
                            <li class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#2c6904]" style="font-variation-settings:'FILL' 1">check_circle</span>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                    <a href="#contact" class="text-[#2c6904] font-bold flex items-center gap-2 group-hover:gap-4 transition-all text-sm">
                        {{ $c0['cta_text'] ?? 'Déposer mon CV' }} <span class="material-symbols-outlined">east</span>
                    </a>
                </div>

                {{-- Carte Entreprises --}}
                @php $c1 = $pqCards[1] ?? []; @endphp
                <div class="group relative overflow-hidden rounded-3xl bg-[#efe6e2] border border-[#c1c9b6] p-10 hover:-translate-y-1 transition-transform duration-300">
                    <div class="mb-6 inline-flex p-4 rounded-2xl bg-[#875212] text-white">
                        <span class="material-symbols-outlined text-4xl">corporate_fare</span>
                    </div>
                    <h3 class="font-sora text-2xl font-bold text-[#1e1b18] mb-4">{{ $c1['title'] ?? 'Entreprises & Fermes' }}</h3>
                    <p class="text-[#41493b] text-base leading-relaxed mb-6">{{ $c1['description'] ?? '' }}</p>
                    <ul class="space-y-3 mb-8 text-[#41493b] font-semibold text-sm">
                        @foreach($c1['list'] ?? ['Présélection de candidats', 'Appui à la définition de postes', 'Plateforme de gestion d\'offres'] as $item)
                            <li class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#875212]" style="font-variation-settings:'FILL' 1">check_circle</span>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                    <a href="#contact" class="text-[#875212] font-bold flex items-center gap-2 group-hover:gap-4 transition-all text-sm">
                        {{ $c1['cta_text'] ?? 'Publier une offre' }} <span class="material-symbols-outlined">east</span>
                    </a>
                </div>
            </div>
        </section>
        @endif

        {{-- ── NOS SERVICES ── --}}
        @if($sectionVisibility['services'] ?? true)
        <section class="py-20 bg-[#F9F7F2]" id="services">
            <div class="px-4 md:px-16 max-w-[1280px] mx-auto">
                <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-4">
                    <div class="max-w-xl">
                        <h2 class="font-sora text-3xl font-bold text-[#2c6904] mb-4">{{ $services['title'] ?? 'Nos Services' }}</h2>
                        <p class="text-[#41493b] text-base">{{ $services['subtitle'] ?? '' }}</p>
                    </div>
                    <span class="material-symbols-outlined text-[#2c6904] text-6xl opacity-20 select-none">agriculture</span>
                </div>
                @php $svcCards = $services['cards'] ?? []; @endphp
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 bg-white p-8 rounded-3xl border border-[#c1c9b6] shadow-sm hover:-translate-y-1 transition-transform duration-300 flex flex-col justify-between">
                        <div>
                            <span class="material-symbols-outlined text-[#2c6904] mb-4 text-3xl block">school</span>
                            <h4 class="font-sora text-xl font-bold mb-3">{{ $svcCards[0]['title'] ?? 'Formation & Orientation' }}</h4>
                            <p class="text-[#41493b] text-base leading-relaxed">{{ $svcCards[0]['description'] ?? '' }}</p>
                        </div>
                        <div class="mt-6 rounded-xl h-48 w-full overflow-hidden bg-[#f5ece7] flex items-center justify-center">
                            <span class="material-symbols-outlined text-[#2c6904] text-8xl opacity-20">eco</span>
                        </div>
                    </div>
                    <div class="bg-[#2c6904] text-white p-8 rounded-3xl hover:-translate-y-1 transition-transform duration-300">
                        <span class="material-symbols-outlined mb-4 text-3xl block">join_inner</span>
                        <h4 class="font-sora text-xl font-bold mb-3">{{ $svcCards[1]['title'] ?? 'Insertion Professionnelle' }}</h4>
                        <p class="opacity-90 text-base leading-relaxed">{{ $svcCards[1]['description'] ?? '' }}</p>
                        @if(!empty($svcCards[1]['stat']))
                            <div class="mt-8 border-t border-white/20 pt-6">
                                <div class="flex items-center gap-3">
                                    <span class="font-sora text-3xl font-bold">{{ explode(' ', $svcCards[1]['stat'])[0] ?? '150+' }}</span>
                                    <span class="text-sm opacity-80">{{ implode(' ', array_slice(explode(' ', $svcCards[1]['stat']), 1)) }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="bg-[#875212] text-white p-8 rounded-3xl hover:-translate-y-1 transition-transform duration-300">
                        <span class="material-symbols-outlined mb-4 text-3xl block">groups</span>
                        <h4 class="font-sora text-xl font-bold mb-3">{{ $svcCards[2]['title'] ?? 'Appui RH aux Entreprises' }}</h4>
                        <p class="opacity-90 text-base leading-relaxed">{{ $svcCards[2]['description'] ?? '' }}</p>
                    </div>
                    <div class="md:col-span-2 bg-[#f5ece7] p-8 rounded-3xl border border-[#c1c9b6] hover:-translate-y-1 transition-transform duration-300 flex items-center gap-8">
                        <div class="hidden sm:block w-1/3 text-center">
                            <span class="material-symbols-outlined text-8xl text-[#2c6904]/10">biotech</span>
                        </div>
                        <div>
                            <h4 class="font-sora text-xl font-bold mb-3 text-[#2c6904]">{{ $svcCards[3]['title'] ?? 'Monitoring & Impact' }}</h4>
                            <p class="text-[#41493b] text-base leading-relaxed">{{ $svcCards[3]['description'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        {{-- ── COMMENT ÇA MARCHE ── --}}
        @if($sectionVisibility['comment'] ?? true)
        <section class="py-20 px-4 md:px-16 max-w-[1280px] mx-auto">
            <h2 class="font-sora text-3xl font-bold text-[#2c6904] text-center mb-16">
                {{ $comment['title'] ?? 'Comment ça marche ?' }}
            </h2>
            <div class="relative">
                <div class="hidden md:block absolute top-8 left-0 w-full h-0.5 bg-[#c1c9b6] z-0"></div>
                <div class="grid md:grid-cols-3 gap-12 relative z-10">
                    @foreach($comment['steps'] ?? [['title'=>'Accueil & Conseil','description'=>'Un premier échange pour comprendre votre profil.'],['title'=>'Enregistrement','description'=>'Inscription sur notre plateforme sécurisée.'],['title'=>'Mise en relation','description'=>'Matching intelligent entre candidats et entreprises.']] as $idx => $step)
                        <div class="text-center group">
                            <div class="w-16 h-16 bg-[#fff8f5] border-4 border-[#2c6904] rounded-full flex items-center justify-center mx-auto mb-6 transition-colors group-hover:bg-[#2c6904] group-hover:text-white font-sora font-bold text-xl text-[#2c6904]">
                                {{ $idx + 1 }}
                            </div>
                            <h4 class="font-sora font-bold text-lg mb-2">{{ $step['title'] }}</h4>
                            <p class="text-[#41493b] text-sm leading-relaxed">{{ $step['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- ── PARTENAIRES ── --}}
        @if($sectionVisibility['partenaires'] ?? true)
        <section class="py-20 bg-[#e9e1dc]/30" id="partenaires">
            <div class="px-4 md:px-16 max-w-[1280px] mx-auto">
                <h2 class="font-semibold text-base text-center text-[#41493b] uppercase tracking-widest mb-12">
                    {{ $partenaires['title'] ?? 'Ils nous font confiance' }}
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-12 items-center justify-items-center opacity-60">
                    @foreach($partenaires['items'] ?? ['YELEMANI', 'CRIC', 'MAAH', 'BFA-OPS'] as $partner)
                        <div class="font-sora text-2xl font-bold text-[#41493b] grayscale">{{ $partner }}</div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- ── CONTACT ── --}}
        <section class="py-20 px-4 md:px-16 max-w-[1280px] mx-auto" id="contact">
            <div class="bg-white rounded-[2rem] overflow-hidden shadow-xl border border-[#c1c9b6] flex flex-col lg:flex-row">
                <div class="lg:w-1/2 p-10 md:p-12 bg-[#2c6904] text-white">
                    <h2 class="font-sora text-3xl font-bold mb-8">{{ $contact['title'] ?? 'Contactez-nous' }}</h2>
                    <p class="text-lg leading-relaxed mb-12 opacity-90">{{ $contact['subtitle'] ?? '' }}</p>
                    <div class="space-y-8">
                        <div class="flex items-start gap-4">
                            <span class="material-symbols-outlined p-2 bg-white/10 rounded-lg flex-shrink-0">location_on</span>
                            <div>
                                <h5 class="font-bold text-sm">Siège Social</h5>
                                <p class="opacity-80 text-sm mt-0.5">{{ $contact['address'] ?? 'Secteur 15, Ouagadougou, Burkina Faso' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <span class="material-symbols-outlined p-2 bg-white/10 rounded-lg flex-shrink-0">call</span>
                            <div>
                                <h5 class="font-bold text-sm">Téléphone</h5>
                                <p class="opacity-80 text-sm mt-0.5">{{ $contact['phone'] ?? '+226 25 30 00 00' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <span class="material-symbols-outlined p-2 bg-white/10 rounded-lg flex-shrink-0">mail</span>
                            <div>
                                <h5 class="font-bold text-sm">Email</h5>
                                <p class="opacity-80 text-sm mt-0.5">{{ $contact['email'] ?? 'contact@agroecobaara.bf' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-16 p-6 bg-white/5 rounded-2xl border border-white/10">
                        <p class="text-sm italic opacity-70">"{{ $contact['hours'] ?? 'Ouvert du Lundi au Vendredi : 08h00 - 16h30' }}"</p>
                    </div>
                </div>
                <div class="lg:w-1/2 p-10 md:p-12 bg-[#fff8f5]">
                    <livewire:landing.contact-form />
                </div>
            </div>
        </section>

    </main>

    {{-- ── FOOTER ── --}}
    <footer class="bg-[#e9e1dc] border-t border-[#c1c9b6] mt-20">
        <div class="w-full py-12 px-4 md:px-16 max-w-[1280px] mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start gap-12 mb-12">
                <div class="max-w-sm">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" class="h-10 w-auto" />
                    </div>
                    <p class="text-[#41493b] text-sm leading-relaxed">
                        Facilitateur d'avenir pour l'agroécologie au Burkina Faso. Connecter les compétences d'aujourd'hui aux défis de demain.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-12">
                    <div>
                        <h6 class="font-bold text-sm text-[#1e1b18] mb-4">Navigation</h6>
                        <ul class="space-y-2 text-sm text-[#41493b]">
                            <li><a href="#accueil" class="hover:text-[#2c6904]">Accueil</a></li>
                            <li><a href="#services" class="hover:text-[#2c6904]">Services</a></li>
                            <li><a href="#partenaires" class="hover:text-[#2c6904]">Partenaires</a></li>
                            <li><a href="#contact" class="hover:text-[#2c6904]">Contact</a></li>
                        </ul>
                    </div>
                    <div>
                        <h6 class="font-bold text-sm text-[#1e1b18] mb-4">Légal</h6>
                        <ul class="space-y-2 text-sm text-[#41493b]">
                            <li><a href="#" class="hover:text-[#2c6904]">Mentions Légales</a></li>
                            <li><a href="#" class="hover:text-[#2c6904]">Confidentialité</a></li>
                            <li><a href="#" class="hover:text-[#2c6904]">Conditions d'utilisation</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="border-t border-[#c1c9b6]/30 pt-8 flex flex-col md:flex-flex-row justify-between items-center gap-4">
                <p class="text-sm text-[#41493b]">© {{ date('Y') }} Agro Eco BAARA. Tous droits réservés.</p>
                <p class="text-xs text-[#717a69]">Association Yelemani · Partenaire CRIC Italie</p>
            </div>
        </div>
    </footer>

</div>