@php
    $sec = $sections ?? collect();

    // ── Hero ──────────────────────────────────────────────────────────────────
    $heroSection  = $sec->get('hero');
    $heroContent  = $heroSection?->content ?? [];
    $heroSlides   = $heroContent['slides'] ?? [];
    $heroLogo     = $heroContent['logo_url'] ?? '/images/logo.jpeg';

    // ── Le Projet ─────────────────────────────────────────────────────────────
    $projetSection   = $sec->get('le_projet');
    $projetContent   = $projetSection?->content ?? [];
    $projetBadge     = $projetContent['badge']      ?? 'NOTRE MISSION';
    $projetTitle     = $projetContent['title']      ?? 'Le Projet';
    $projetParagraphs= $projetContent['paragraphs'] ?? [];
    $projetImage     = $projetContent['image_url']  ?? '';

    // ── Audiences ─────────────────────────────────────────────────────────────
    $audSection  = $sec->get('audiences');
    $audContent  = $audSection?->content ?? [];
    $audTitle    = $audContent['title'] ?? 'Agro Eco BAARA S\'ADRESSE à VOUS';
    $audCards    = $audContent['cards'] ?? [];

    // ── Guichet ───────────────────────────────────────────────────────────────
    $guichetSection  = $sec->get('guichet');
    $guichetContent  = $guichetSection?->content ?? [];
    $guichetTitle    = $guichetContent['title']          ?? 'UN GUICHET POUR L\'EMPLOI';
    $guichetDesc     = $guichetContent['description']    ?? '';
    $guichetLoc      = $guichetContent['localisation']   ?? '';
    $guichetHoraires = $guichetContent['horaires']       ?? '';
    $guichetContacts = $guichetContent['contacts']       ?? '';
    $guichetImg      = $guichetContent['image_url']      ?? '';
    $guichetCaption  = $guichetContent['image_caption']  ?? '';

    // ── Ce que vous pouvez trouver ────────────────────────────────────────────
    $ceQueSection  = $sec->get('ce_que_vous_pouvez_trouver');
    $ceQueContent  = $ceQueSection?->content ?? [];
    $ceQueTitle    = $ceQueContent['title']   ?? 'CE QUE VOUS POUVEZ TROUVER';
    $ceQueColumns  = $ceQueContent['columns'] ?? [];

    // ── Comment ───────────────────────────────────────────────────────────────
    $commentSection  = $sec->get('comment');
    $commentContent  = $commentSection?->content ?? [];
    $commentTitle    = $commentContent['title'] ?? 'COMMENT ÇA MARCHE ?';
    $commentSteps    = $commentContent['steps'] ?? [];

    // ── Autres Services ───────────────────────────────────────────────────────
    $autresSection   = $sec->get('autres_services');
    $autresContent   = $autresSection?->content ?? [];
    $autresTitle     = $autresContent['title']         ?? 'AUTRES SERVICES';
    $autresDesc      = $autresContent['description']   ?? '';
    $autresFbLink    = $autresContent['facebook_link'] ?? '#';
    $autresFbText    = $autresContent['facebook_text'] ?? 'Suivez-nous sur Facebook';
    $autresServices  = $autresContent['services']      ?? [];

    // ── Partenaires ───────────────────────────────────────────────────────────
    $partSection  = $sec->get('partenaires');
    $partContent  = $partSection?->content ?? [];
    $partTitle    = $partContent['title'] ?? 'NOS PARTENAIRES';
    $partItems    = $partContent['items'] ?? [];

    // ── Témoignages ───────────────────────────────────────────────────────────
    $temoSection  = $sec->get('temoignages');
    $temoContent  = $temoSection?->content ?? [];
    $temoTitle    = $temoContent['title'] ?? 'TÉMOIGNAGES';
    $temoItems    = $temoContent['items'] ?? [];

    // ── Médiathèque ───────────────────────────────────────────────────────────
    $mediaSection  = $sec->get('mediatheque');
    $mediaContent  = $mediaSection?->content ?? [];
    $mediaTitle    = $mediaContent['title']       ?? 'MÉDIATHÈQUE';
    $mediaDesc     = $mediaContent['description'] ?? 'Découvrez nos activités à travers nos visuels.';
    $mediaPhotos      = $mediaContent['photos']      ?? [];
    $mediaCategories  = $mediaContent['categories'] ?? [
        ['key' => 'terrain',   'label' => 'Terrain'],
        ['key' => 'formation', 'label' => 'Formation'],
        ['key' => 'evenement', 'label' => 'Événement'],
    ];
    $catColorPalette  = [
        'bg-primary text-on-primary',
        'bg-secondary text-on-secondary',
        'bg-tertiary text-on-tertiary',
        'bg-primary-container text-on-primary-container',
        'bg-secondary-container text-on-secondary-container',
        'bg-tertiary-container text-on-tertiary-container',
    ];
    $firstCatKey = $mediaCategories[0]['key'] ?? 'terrain';
    $catLabels = collect($mediaCategories)->pluck('label', 'key')->all();
    $catColors = [];
    foreach ($mediaCategories as $i => $cat) {
        $catColors[$cat['key']] = $catColorPalette[$i % count($catColorPalette)];
    }
    $mediaImages = collect($mediaPhotos)->filter(fn ($p) => ($p['type'] ?? 'image') !== 'video')->values();
    $mediaVideos = collect($mediaPhotos)->filter(fn ($p) => ($p['type'] ?? 'image') === 'video')->values();
    $hasMediaPhotos = $mediaImages->isNotEmpty();
    $hasMediaVideos = $mediaVideos->isNotEmpty();
    $contactSection  = $sec->get('contact');
    $contactContent  = $contactSection?->content ?? [];
    $contactTitle    = $contactContent['title']    ?? 'Contactez-nous';
    $contactSubtitle = $contactContent['subtitle'] ?? '';
    $contactAddress  = $contactContent['address']  ?? '';
    $contactPhone    = $contactContent['phone']    ?? '';
    $contactEmail    = $contactContent['email']    ?? '';

    // Avatar colors map for testimonials
    $avatarColors = [
        'primary'   => 'bg-primary-container text-primary',
        'secondary' => 'bg-secondary-container text-secondary',
        'tertiary'  => 'bg-tertiary-fixed text-tertiary',
    ];
@endphp
<div>
<!-- TopAppBar -->
<header class="bg-surface docked full-width top-0 border-b border-outline-variant z-50 sticky" x-data="{ mobileMenu: false }">
<nav class="flex justify-between items-center w-full px-margin-mobile md:px-margin-desktop py-4 max-w-container-max mx-auto">
<div class="flex items-center gap-4">
<img alt="Agro Eco BAARA Logo" class="h-20 w-auto object-contain" src="{{ $heroLogo }}"/>
</div>
<div class="hidden md:flex items-center gap-8">
<a class="text-primary border-b-2 border-primary pb-1 font-label-bold hover:text-primary transition-colors" href="#">Accueil</a>
<a class="text-on-surface-variant font-label-bold hover:text-primary transition-colors" href="#audiences">S'adresse à vous</a>
<a class="text-on-surface-variant font-label-bold hover:text-primary transition-colors" href="#guichet">Le Guichet</a>
<a class="text-on-surface-variant font-label-bold hover:text-primary transition-colors" href="#partenaires">Partenaires</a>
<a class="text-on-surface-variant font-label-bold hover:text-primary transition-colors" href="#mediatheque">Médiathèque</a>
<a class="text-on-surface-variant font-label-bold hover:text-primary transition-colors" href="{{ route('bibliotheque') }}">Bibliothèque</a>
<a class="text-on-surface-variant font-label-bold hover:text-primary transition-colors" href="#contact">Contactez-nous</a>
@auth
<div class="relative" x-data="{ open: false }">
<button @click="open = !open" class="flex items-center gap-2 bg-primary text-on-primary px-4 py-2 rounded-lg font-label-bold hover:opacity-90 transition-all">
<span class="material-symbols-outlined text-lg">account_circle</span>
{{ Auth::user()->name }}
<span class="material-symbols-outlined text-sm" x-text="open ? 'expand_less' : 'expand_more'"></span>
</button>
<div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-outline-variant py-2 z-50">
<a href="{{ route('admin.candidates.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-on-surface hover:bg-surface-container-low transition-colors">
<span class="material-symbols-outlined text-base">admin_panel_settings</span>
Administration
</a>
<form method="POST" action="{{ route('logout') }}">
@csrf
<button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
<span class="material-symbols-outlined text-base">logout</span>
Déconnexion
</button>
</form>
</div>
</div>
@else
<a href="{{ route('login') }}" class="bg-primary text-on-primary px-6 py-2 rounded-lg font-label-bold hover:opacity-90 transition-all">Connexion</a>
@endauth
</div>
<button @click="mobileMenu = !mobileMenu" class="md:hidden text-primary">
<span class="material-symbols-outlined text-3xl" x-text="mobileMenu ? 'close' : 'menu'"></span>
</button>
</nav>

{{-- Menu mobile --}}
<div x-show="mobileMenu" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="md:hidden border-t border-outline-variant bg-surface px-margin-mobile py-4 space-y-2">
<a @click="mobileMenu = false" class="block py-2.5 px-4 rounded-lg font-label-bold text-primary bg-primary/5" href="#">Accueil</a>
<a @click="mobileMenu = false" class="block py-2.5 px-4 rounded-lg font-label-bold text-on-surface-variant hover:bg-surface-container-low" href="#audiences">S'adresse à vous</a>
<a @click="mobileMenu = false" class="block py-2.5 px-4 rounded-lg font-label-bold text-on-surface-variant hover:bg-surface-container-low" href="#guichet">Le Guichet</a>
<a @click="mobileMenu = false" class="block py-2.5 px-4 rounded-lg font-label-bold text-on-surface-variant hover:bg-surface-container-low" href="#partenaires">Partenaires</a>
<a @click="mobileMenu = false" class="block py-2.5 px-4 rounded-lg font-label-bold text-on-surface-variant hover:bg-surface-container-low" href="#mediatheque">Médiathèque</a>
<a @click="mobileMenu = false" class="block py-2.5 px-4 rounded-lg font-label-bold text-on-surface-variant hover:bg-surface-container-low" href="{{ route('bibliotheque') }}">Bibliothèque</a>
<a @click="mobileMenu = false" class="block py-2.5 px-4 rounded-lg font-label-bold text-on-surface-variant hover:bg-surface-container-low" href="#contact">Contactez-nous</a>
<div class="pt-2 border-t border-outline-variant">
@auth
<a href="{{ route('admin.candidates.index') }}" class="block py-2.5 px-4 rounded-lg font-label-bold text-primary hover:bg-primary/5">Administration</a>
<form method="POST" action="{{ route('logout') }}">
@csrf
<button type="submit" class="w-full text-left py-2.5 px-4 rounded-lg font-label-bold text-red-600 hover:bg-red-50">Déconnexion</button>
</form>
@else
<a href="{{ route('login') }}" class="block py-2.5 px-4 rounded-lg font-label-bold text-white bg-primary text-center hover:opacity-90">Connexion</a>
@endauth
</div>
</div>
</header>
<main>

@include('livewire.landing.partials.hero-slider')

{{-- ═══════════════════════════════════════ LE PROJET ════════════════════════════════════════ --}}
@if($projetSection?->is_active !== false)
<section class="py-20 bg-surface-container-low">
<div class="px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
<div class="flex flex-col lg:flex-row items-center gap-12">
<div class="flex-1" data-animate="fade-right">
<div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-container text-on-primary-container rounded-full font-label-bold text-sm mb-6">
<span class="material-symbols-outlined text-sm">rocket_launch</span>
{{ $projetBadge }}
</div>
<h2 class="font-headline-lg text-headline-lg text-primary mb-4">{{ $projetTitle }}</h2>
<div class="h-1.5 w-24 bg-secondary rounded-full mb-6"></div>
<div class="prose prose-lg text-on-surface-variant max-w-none space-y-6">
@foreach($projetParagraphs as $para)
<p class="font-body-lg leading-9">{!! $para !!}</p>
@endforeach
</div>
</div>
<div class="lg:w-1/3 flex justify-center" data-animate="zoom-in" data-delay="200">
<div class="relative">
<div class="absolute -inset-4 bg-primary/10 rounded-full blur-3xl"></div>
@if($projetImage)
<img alt="{{ $projetTitle }}" class="relative max-h-64 w-auto object-contain rounded-2xl" src="{{ $projetImage }}"/>
@else
<span class="material-symbols-outlined text-[180px] text-primary relative">eco</span>
@endif
</div>
</div>
</div>
</div>
</section>
@endif

{{-- ═══════════════════════════════════════ S'ADRESSE À VOUS ═════════════════════════════════ --}}
@if($audSection?->is_active !== false)
<section class="py-20 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto" id="audiences">
<div class="text-center mb-16" data-animate="fade-up">
<h2 class="font-headline-lg text-headline-lg text-primary mb-4 uppercase">{{ $audTitle }}</h2>
<div class="h-1.5 w-24 bg-secondary mx-auto rounded-full"></div>
</div>
<div class="grid md:grid-cols-3 gap-8">
@php
    $audBgs    = ['bg-surface-container-low','bg-surface-container-high','bg-tertiary-fixed text-on-tertiary-fixed'];
    $audIconBg = ['bg-primary-container text-on-primary-container','bg-secondary-container text-on-secondary-container','bg-tertiary-container text-on-tertiary-container'];
    $audCtaColor = ['text-primary','text-secondary',''];
@endphp
@foreach($audCards as $ci => $card)
<div class="group relative overflow-hidden rounded-3xl border border-outline-variant p-8 bento-item card-lift {{ $audBgs[$ci] ?? 'bg-surface-container-low' }}"
     data-animate="fade-up" data-delay="{{ $ci * 150 }}">
<div class="mb-6 inline-flex p-4 rounded-2xl {{ $audIconBg[$ci] ?? 'bg-primary-container text-on-primary-container' }}">
<span class="material-symbols-outlined text-4xl">{{ $card['icon'] ?? 'star' }}</span>
</div>
<h3 class="font-headline-md text-headline-md mb-4">{{ $card['title'] ?? '' }}</h3>
<p class="font-body-md text-body-md mb-6 opacity-80">{{ $card['description'] ?? '' }}</p>
<a href="#guichet" class="font-label-bold flex items-center gap-2 group-hover:gap-4 transition-all {{ $audCtaColor[$ci] ?? '' }}">
{{ $card['cta_text'] ?? '' }} <span class="material-symbols-outlined">east</span>
</a>
</div>
@endforeach
</div>
</section>
@endif

{{-- ═══════════════════════════════════════ LE GUICHET ═══════════════════════════════════════ --}}
@if($guichetSection?->is_active !== false)
<section class="py-20 text-white overflow-hidden relative" id="guichet" style="background-color: #69a313;">
<div class="absolute right-0 top-0 opacity-10 -mr-20 -mt-20">
<span class="material-symbols-outlined text-[300px]">meeting_room</span>
</div>
<div class="px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto relative z-10">
<div class="grid lg:grid-cols-2 gap-12 items-center">
<div data-animate="fade-left">
<h2 class="font-headline-lg text-headline-lg mb-6">{{ $guichetTitle }}</h2>
<p class="font-body-lg mb-10 opacity-90">{{ $guichetDesc }}</p>
<div class="space-y-6">
@if($guichetLoc)
<div class="flex items-center gap-4">
<span class="material-symbols-outlined p-3 bg-white/10 rounded-full">location_on</span>
<div>
<p class="font-label-bold">Localisation</p>
<p class="opacity-80">{{ $guichetLoc }}</p>
</div>
</div>
@endif
@if($guichetHoraires)
<div class="flex items-center gap-4">
<span class="material-symbols-outlined p-3 bg-white/10 rounded-full">schedule</span>
<div>
<p class="font-label-bold">Horaires d'ouverture</p>
<p class="opacity-80">{{ $guichetHoraires }}</p>
</div>
</div>
@endif
@if($guichetContacts)
<div class="flex items-center gap-4">
<span class="material-symbols-outlined p-3 bg-white/10 rounded-full">contact_support</span>
<div>
<p class="font-label-bold">Contacts</p>
<p class="opacity-80">{{ $guichetContacts }}</p>
</div>
</div>
@endif
</div>
</div>
@if($guichetImg)
<div class="bg-white/10 backdrop-blur-sm p-8 rounded-3xl border border-white/20" data-animate="fade-right" data-delay="200">
<img alt="{{ $guichetTitle }}" class="rounded-2xl w-full h-64 object-cover mb-6" src="{{ $guichetImg }}"/>
@if($guichetCaption)
<p class="italic text-center opacity-80">{{ $guichetCaption }}</p>
@endif
</div>
@endif
</div>
</div>
</section>
@endif

{{-- ═════════════════════════════ CE QUE VOUS POUVEZ TROUVER ════════════════════════════════ --}}
@if($ceQueSection?->is_active !== false && count($ceQueColumns))
<section class="py-20 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
<h2 class="font-headline-lg text-headline-lg text-primary text-center mb-4 uppercase" data-animate="fade-up">{{ $ceQueTitle }}</h2>
<div class="h-1.5 w-24 bg-secondary mx-auto rounded-full mb-12" data-animate="fade-up" data-delay="100"></div>
<div class="grid md:grid-cols-2 gap-12">
@php $colBorders = ['border-primary-container','border-secondary-container']; @endphp
@foreach($ceQueColumns as $ci => $col)
<div class="bg-surface-container-lowest p-10 rounded-3xl border-2 {{ $colBorders[$ci] ?? 'border-primary-container' }} shadow-lg card-lift"
     data-animate="fade-up" data-delay="{{ 150 + $ci * 150 }}">
<h3 class="font-headline-md text-headline-md text-{{ $col['color'] ?? 'primary' }} mb-8 flex items-center gap-3">
<span class="material-symbols-outlined text-3xl">{{ $col['icon'] ?? 'star' }}</span>
{{ $col['title'] ?? '' }}
</h3>
<ul class="space-y-4">
@foreach($col['items'] ?? [] as $item)
<li class="flex items-start gap-4">
<span class="material-symbols-outlined text-{{ $col['color'] ?? 'primary' }} mt-1">check_circle</span>
<span class="font-body-md">{{ $item }}</span>
</li>
@endforeach
</ul>
</div>
@endforeach
</div>
</section>
@endif

{{-- ══════════════════════════════════ COMMENT ÇA MARCHE ═════════════════════════════════════ --}}
@if($commentSection?->is_active !== false && count($commentSteps))
<section class="py-20 bg-surface-container-low">
<div class="px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
<h2 class="font-headline-lg text-headline-lg text-primary text-center mb-4 uppercase" data-animate="fade-up">{{ $commentTitle }}</h2>
<div class="h-1.5 w-24 bg-secondary mx-auto rounded-full mb-12" data-animate="fade-up" data-delay="100"></div>
<div class="relative">
<div class="hidden md:block absolute top-1/2 left-0 w-full h-0.5 bg-outline-variant -translate-y-1/2 z-0"></div>
<div class="grid md:grid-cols-{{ count($commentSteps) }} gap-12 relative z-10">
@foreach($commentSteps as $step)
<div class="text-center group" data-animate="fade-up" data-delay="{{ $loop->index * 150 }}">
<div class="w-20 h-20 bg-surface border-4 border-primary rounded-full flex items-center justify-center mx-auto mb-6 transition-all group-hover:bg-primary group-hover:text-white shadow-lg">
<span class="font-headline-sm">{{ $step['number'] ?? ($loop->index + 1) }}</span>
</div>
<h4 class="font-headline-sm mb-3">{{ $step['title'] ?? '' }}</h4>
<p class="text-on-surface-variant font-body-sm px-4">{{ $step['description'] ?? '' }}</p>
</div>
@endforeach
</div>
</div>
</div>
</section>
@endif

{{-- ══════════════════════════════════ AUTRES SERVICES ═══════════════════════════════════════ --}}
@if($autresSection?->is_active !== false)
<section class="py-20 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
<div class="bg-surface-container-low rounded-[2rem] p-8 md:p-12 border border-outline-variant flex flex-col md:flex-row items-center gap-12" data-animate="fade-up">
<div class="flex-1">
<h2 class="font-headline-lg text-headline-lg text-primary mb-4 uppercase">{{ $autresTitle }}</h2>
<div class="h-1.5 w-24 bg-secondary rounded-full mb-6"></div>
<p class="font-body-lg text-on-surface-variant mb-8">{{ $autresDesc }}</p>
<div class="grid sm:grid-cols-2 gap-6 mb-10">
@foreach($autresServices as $svc)
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-primary">{{ $svc['icon'] ?? 'star' }}</span>
<span class="font-label-bold">{{ $svc['label'] ?? '' }}</span>
</div>
@endforeach
</div>
@if($autresFbLink && $autresFbLink !== '#')
<a class="inline-flex items-center gap-3 bg-primary text-on-primary px-6 py-3 rounded-xl hover:bg-primary-container hover:text-on-primary-container border border-primary transition-all font-label-bold" href="{{ $autresFbLink }}" target="_blank">
<span class="material-symbols-outlined">facebook</span>
{{ $autresFbText }}
</a>
@endif
</div>
<div class="flex-shrink-0 w-full md:w-1/3">
<div class="rounded-2xl overflow-hidden border border-outline-variant shadow-sm">
<iframe src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fprofile.php%3Fid%3D61590282042498&tabs=timeline&width=340&height=400&small_header=true&adapt_container_width=true&hide_cover=false&show_facepile=true&appId" width="340" height="400" style="border:none;overflow:hidden;width:100%;max-width:340px;" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" loading="lazy"></iframe>
</div>
</div>
</div>
</section>
@endif

{{-- ══════════════════════════════════ PARTENAIRES ═══════════════════════════════════════════ --}}
@if($partSection?->is_active !== false && count($partItems))
<section class="py-20 bg-surface-container-low" id="partenaires">
<div class="px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
<h2 class="font-headline-lg text-headline-lg text-primary text-center mb-4 uppercase" data-animate="fade-up">{{ $partTitle }}</h2>
<div class="h-1.5 w-24 bg-secondary mx-auto rounded-full mb-12" data-animate="fade-up" data-delay="100"></div>

<div class="relative px-14" data-animate="fade-up" data-delay="200">
    <button type="button" id="partners-prev" class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-12 h-12 bg-surface border border-outline-variant rounded-full shadow-lg flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-all">
        <span class="material-symbols-outlined">chevron_left</span>
    </button>

    <div id="partners-track" class="overflow-hidden">
        <div id="partners-inner" class="flex gap-6 transition-transform duration-500 ease-in-out">
            @foreach($partItems as $partner)
            <div class="bg-surface p-8 rounded-3xl border border-outline-variant flex flex-col items-center text-center flex-shrink-0 w-full md:w-[calc(33.333%-1rem)]">
                <div class="h-24 flex items-center justify-center mb-6">
                    <img alt="{{ $partner['name'] ?? '' }}" class="max-h-full w-auto object-contain" src="{{ $partner['logo'] ?? '' }}"/>
                </div>
                <h4 class="font-headline-sm mb-2">{{ $partner['name'] ?? '' }}</h4>
                <p class="text-sm text-on-surface-variant mb-6 flex-grow">{{ $partner['description'] ?? '' }}</p>
                @if(!empty($partner['website']))
                <div class="flex gap-4">
                    <a class="flex items-center gap-1 text-primary text-xs font-label-bold hover:underline" href="{{ $partner['website'] }}" {{ $partner['website'] !== '#' ? 'target="_blank"' : '' }}>
                        <span class="material-symbols-outlined text-sm">{{ $partner['social_icon'] ?? 'language' }}</span>
                        {{ $partner['social_label'] ?? 'Site Web' }}
                    </a>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <button type="button" id="partners-next" class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-12 h-12 bg-surface border border-outline-variant rounded-full shadow-lg flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-all">
        <span class="material-symbols-outlined">chevron_right</span>
    </button>
</div>
<div id="partners-dots" class="flex justify-center gap-2 mt-8"></div>
</div>
</section>
@endif

@script
<script>
(function () {
    var inner   = document.getElementById('partners-inner');
    var dotsEl  = document.getElementById('partners-dots');
    var btnPrev = document.getElementById('partners-prev');
    var btnNext = document.getElementById('partners-next');
    if(!inner||!dotsEl||!btnPrev||!btnNext) return;

    var total   = inner.children.length;
    var perView = window.innerWidth >= 768 ? 3 : 1;
    var current = 0;
    var maxIdx  = Math.max(0, total - perView);
    var autoTimer;

    for (var i = 0; i <= maxIdx; i++) {
        var d = document.createElement('button');
        d.type = 'button';
        d.className = 'w-2.5 h-2.5 rounded-full bg-outline-variant transition-all';
        d.setAttribute('data-idx', i);
        d.addEventListener('click', function () { goTo(+this.getAttribute('data-idx')); });
        dotsEl.appendChild(d);
    }

    function updateDots() {
        Array.from(dotsEl.children).forEach(function (d, i) {
            d.className = i === current
                ? 'w-6 h-2.5 rounded-full bg-primary transition-all'
                : 'w-2.5 h-2.5 rounded-full bg-outline-variant transition-all';
        });
    }

    function goTo(idx) {
        current = Math.max(0, Math.min(idx, maxIdx));
        var cardW = inner.children[0] ? inner.children[0].offsetWidth : 0;
        var gap   = 24;
        inner.style.transform = 'translateX(-' + current * (cardW + gap) + 'px)';
        updateDots();
        resetAuto();
    }

    function next() { goTo(current >= maxIdx ? 0 : current + 1); }
    function prev() { goTo(current <= 0 ? maxIdx : current - 1); }

    function resetAuto() {
        clearInterval(autoTimer);
        autoTimer = setInterval(next, 3500);
    }

    btnNext.addEventListener('click', next);
    btnPrev.addEventListener('click', prev);

    window.addEventListener('resize', function () {
        perView = window.innerWidth >= 768 ? 3 : 1;
        maxIdx  = Math.max(0, total - perView);
        goTo(0);
    });

    goTo(0);
    resetAuto();
})();
</script>
@endscript

{{-- ══════════════════════════════════ TÉMOIGNAGES ═══════════════════════════════════════════ --}}
@if($temoSection?->is_active !== false && count($temoItems))
<section class="py-20 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
<h2 class="font-headline-lg text-headline-lg text-primary text-center mb-4" data-animate="fade-up">{{ $temoTitle }}</h2>
<div class="h-1.5 w-24 bg-secondary mx-auto rounded-full mb-12" data-animate="fade-up" data-delay="100"></div>
<div class="grid md:grid-cols-3 gap-8">
@foreach($temoItems as $temo)
<div class="bg-surface-container-low p-8 rounded-3xl border border-outline-variant relative card-lift"
     data-animate="fade-up" data-delay="{{ $loop->index * 150 }}">
<span class="material-symbols-outlined text-6xl text-primary/10 absolute top-4 right-4">format_quote</span>
<div class="flex items-center gap-4 mb-6">
<div class="w-14 h-14 rounded-full flex items-center justify-center {{ $avatarColors[$temo['avatar_color'] ?? 'primary'] ?? 'bg-primary-container text-primary' }}">
<span class="material-symbols-outlined">person</span>
</div>
<div>
<p class="font-label-bold">{{ $temo['name'] ?? '' }}</p>
<p class="text-xs text-on-surface-variant">{{ $temo['role'] ?? '' }}</p>
</div>
</div>
<p class="italic text-on-surface-variant">"{{ $temo['text'] ?? '' }}"</p>
</div>
@endforeach
</div>
</section>
@endif

@include('livewire.landing.partials.mediatheque-section')

{{-- ══════════════════════════════════════ CONTACT ═══════════════════════════════════════════ --}}
@if($contactSection?->is_active !== false)
<section class="py-20 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto" id="contact">
<div class="bg-white rounded-[2rem] overflow-hidden shadow-xl border border-outline-variant flex flex-col lg:flex-row">
<div class="lg:w-1/2 p-12 bg-primary text-on-primary" data-animate="fade-left">
<h2 class="font-headline-lg text-headline-lg mb-4">{{ $contactTitle }}</h2>
<div class="h-1.5 w-24 bg-secondary rounded-full mb-8"></div>
<p class="font-body-lg mb-12 opacity-90">{{ $contactSubtitle }}</p>
<div class="space-y-8">
@if($contactAddress)
<div class="flex items-start gap-4">
<span class="material-symbols-outlined p-2 bg-white/10 rounded-lg">location_on</span>
<div>
<h5 class="font-label-bold">Siège Social</h5>
<p class="opacity-80">{{ $contactAddress }}</p>
</div>
</div>
@endif
@if($contactPhone)
<div class="flex items-start gap-4">
<span class="material-symbols-outlined p-2 bg-white/10 rounded-lg">call</span>
<div>
<h5 class="font-label-bold">Téléphone</h5>
<p class="opacity-80">{{ $contactPhone }}</p>
</div>
</div>
@endif
@if($contactEmail)
<div class="flex items-start gap-4">
<span class="material-symbols-outlined p-2 bg-white/10 rounded-lg">mail</span>
<div>
<h5 class="font-label-bold">Email</h5>
<p class="opacity-80">{{ $contactEmail }}</p>
</div>
</div>
@endif
</div>
</div>
<div class="lg:w-1/2 p-12 bg-surface" data-animate="fade-right" data-delay="200">
<livewire:landing.contact-form />
</div>
</div>
</section>
@endif

</main>
<!-- Footer -->
<footer class="bg-surface-container-highest full-width border-t border-outline-variant mt-20">
<div class="w-full py-12 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">

    {{-- Rangée principale --}}
    <div class="flex flex-col lg:flex-row justify-between items-start gap-12 mb-12">

        {{-- Logo + description --}}
        <div class="max-w-xs flex-shrink-0" data-animate="fade-up">
            <img alt="Agro Eco BAARA Logo" class="h-16 w-auto object-contain mb-4"
                 src="https://lh3.googleusercontent.com/aida-public/AB6AXuDuRijQqvLp95sSNJwMjLI846xn1Rab8bMWm4HXf2LoeFhSSJAV2H3hkdFznbOcXXc7xwEPkwgr6yjCndWw0vhacjZOsgGZEDO0gInmfHIf657Zemle0pmNnUVdBkNWCOx6TOt2UrH_YUA955jayCGr6ZsHkiccenXgjmHpRmrfgnPWy1kgXE5uBckIHGDhPKhE9nXK5mXkD-UL0qoKFcEDgtn8qhEF6YORJ9LE3sLjr1Xe50pV3eG05QpuovYzBoUxGufwtjyr903a"/>
            <p class="text-on-surface-variant font-body-sm">Connecter les compétences d'aujourd'hui aux défis de demain.</p>
        </div>

        {{-- Liens navigation --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-10 flex-shrink-0" data-animate="fade-up" data-delay="200">
            <div>
                <h6 class="font-label-bold text-on-surface mb-4">Navigation</h6>
                <ul class="space-y-2 text-body-sm text-on-surface-variant">
                    <li><a class="hover:text-primary" href="#">Accueil</a></li>
                    <li><a class="hover:text-primary" href="#audiences">Public</a></li>
                    <li><a class="hover:text-primary" href="#guichet">Le Guichet</a></li>
                    <li><a class="hover:text-primary" href="#partenaires">Partenaires</a></li>
                </ul>
            </div>
            <div>
                <h6 class="font-label-bold text-on-surface mb-4">Ressources</h6>
                <ul class="space-y-2 text-body-sm text-on-surface-variant">
                    <li><a class="hover:text-primary" href="#mediatheque">Médiathèque</a></li>
                    <li><a class="hover:text-primary" href="{{ route('bibliotheque') }}">Bibliothèque</a></li>
                    <li><a class="hover:text-primary" href="#contact">Contactez-nous</a></li>
                </ul>
            </div>
            <div>
                <h6 class="font-label-bold text-on-surface mb-4">Légal</h6>
                <ul class="space-y-2 text-body-sm text-on-surface-variant">
                    <li><a class="hover:text-primary" href="{{ route('privacy') }}">Politique de confidentialité</a></li>
                </ul>
            </div>
        </div>


    </div>

    {{-- Barre de copyright --}}
    <div class="border-t border-outline-variant/30 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
        <p class="text-body-sm text-on-surface-variant">© 2026 Agro Eco Baara. Tous droits réservés. Conçu par <a href="https://yam-pukri.org" target="_blank" class="hover:text-primary underline">Yam Pukri</a></p>
    </div>

</div>
</footer>
</div>
