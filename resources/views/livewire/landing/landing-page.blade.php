@php
    $sec = $sections ?? collect();

    // ── Hero ──────────────────────────────────────────────────────────────────
    $heroSection  = $sec->get('hero');
    $heroSlides   = $heroSection?->content['slides'] ?? [];

    // ── Le Projet ─────────────────────────────────────────────────────────────
    $projetSection   = $sec->get('le_projet');
    $projetContent   = $projetSection?->content ?? [];
    $projetBadge     = $projetContent['badge']      ?? 'NOTRE MISSION';
    $projetTitle     = $projetContent['title']      ?? 'Le Projet';
    $projetParagraphs= $projetContent['paragraphs'] ?? [];

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
    $mediaDesc     = $mediaContent['description'] ?? 'Découvrez nos activités à travers les photos du terrain.';
    $mediaPhotos   = $mediaContent['photos']      ?? [];


    // ── Contact ───────────────────────────────────────────────────────────────
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

    // Category badge colors map for mediatheque
    $catColors = [
        'terrain'   => 'bg-primary text-on-primary',
        'formation' => 'bg-secondary text-on-secondary',
        'evenement' => 'bg-tertiary text-on-tertiary',
    ];
    $catLabels = [
        'terrain'   => 'Terrain',
        'formation' => 'Formation',
        'evenement' => 'Événement',
    ];
@endphp
<div>
<!-- TopAppBar -->
<header class="bg-surface docked full-width top-0 border-b border-outline-variant z-50 sticky">
<nav class="flex justify-between items-center w-full px-margin-mobile md:px-margin-desktop py-4 max-w-container-max mx-auto">
<div class="flex items-center gap-4">
<img alt="Agro Eco BAARA Logo" class="h-20 w-auto object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDAzpbcglFrXp83qaNSplq7pnweoWJYke3uk9-PpO8jog7JvjW3_LxAkvux17_NMZa97soNde3PlU9tJTqNc_XXirQ88fB1SMJvZihfaRaddGDfwe_tvCFRRhX6dwUy66A4sy4jpN6mfuIsEEDBTzVmmLLo_mYvJwSdMohLg9UVqfdArVCb8J29ZXG59ihBUxdPdnxB9COpXlh1S_7Egh5s8K0dECj4AqbxgTRZRS-LSUbJ7qxFKJn9GmPIb3nEOLeqnA1VU6enk1Pr"/>
</div>
<div class="hidden md:flex items-center gap-8">
<a class="text-primary border-b-2 border-primary pb-1 font-label-bold hover:text-primary transition-colors" href="#">Accueil</a>
<a class="text-on-surface-variant font-label-bold hover:text-primary transition-colors" href="#audiences">S'adresse à vous</a>
<a class="text-on-surface-variant font-label-bold hover:text-primary transition-colors" href="#guichet">Le Guichet</a>
<a class="text-on-surface-variant font-label-bold hover:text-primary transition-colors" href="#partenaires">Partenaires</a>
<a class="text-on-surface-variant font-label-bold hover:text-primary transition-colors" href="#mediatheque">Médiathèque</a>
<a class="text-on-surface-variant font-label-bold hover:text-primary transition-colors" href="#contact">Contact</a>
<a href="{{ route('login') }}" class="bg-primary text-on-primary px-6 py-2 rounded-lg font-label-bold hover:opacity-90 transition-all">Connexion</a>
</div>
<button class="md:hidden text-primary">
<span class="material-symbols-outlined text-3xl">menu</span>
</button>
</nav>
</header>
<main>

{{-- ═══════════════════════════════════════ HERO SLIDER ══════════════════════════════════════ --}}
<style>
#hs{position:relative;height:80vh;overflow:hidden;background:#111;}
.hs-slide{position:absolute;inset:0;height:100%;display:flex;align-items:center;opacity:0;transition:opacity .8s ease;pointer-events:none;z-index:0;}
.hs-slide.on{opacity:1;pointer-events:auto;z-index:1;}
.hs-slide img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;}
.hs-overlay{position:absolute;inset:0;background:linear-gradient(to right,rgba(0,0,0,.72),transparent);z-index:1;}
.hs-body{position:relative;z-index:2;width:100%;padding:0 1rem;max-width:1280px;margin:0 auto;}
@media(min-width:768px){.hs-body{padding:0 4rem;}}
.hs-btn-nav{position:absolute;top:50%;transform:translateY(-50%);z-index:10;width:48px;height:48px;border-radius:9999px;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s;}
.hs-btn-nav:hover{background:rgba(255,255,255,.36);}
#hs-dot-wrap{position:absolute;bottom:1.5rem;left:50%;transform:translateX(-50%);z-index:10;display:flex;gap:8px;align-items:center;}
.hs-dot{height:8px;border-radius:9999px;border:none;cursor:pointer;transition:all .3s;background:rgba(255,255,255,.4);}
.hs-dot.on{width:32px!important;background:#fff;}
</style>

@if($heroSection?->is_active !== false)
<section id="hs">
    @foreach($heroSlides as $i => $slide)
    <div class="hs-slide {{ $i === 0 ? 'on' : '' }}">
        <img src="{{ $slide['image_url'] ?? '' }}" alt="{{ $slide['title'] ?? '' }}" />
        <div class="hs-overlay"></div>
        <div class="hs-body text-white">
            <div style="max-width:48rem">
                <h1 class="font-display-hero-mobile md:font-display-hero text-display-hero-mobile md:text-display-hero mb-2">{{ $slide['title'] ?? '' }}</h1>
                @if(!empty($slide['subtitle']))
                <h2 class="font-headline-lg text-headline-lg mb-6 text-primary-fixed">{{ $slide['subtitle'] }}</h2>
                @endif
                @if(!empty($slide['description']))
                <p class="font-body-lg text-body-lg mb-8 text-surface-container-highest" style="max-width:42rem">{{ $slide['description'] }}</p>
                @endif
                <div style="display:flex;flex-wrap:wrap;gap:1rem">
                    @if(!empty($slide['cta_primary_text']))
                    <a href="{{ $slide['cta_primary_link'] ?? '#' }}" class="bg-primary text-on-primary px-8 py-4 rounded-xl font-headline-sm" style="display:inline-flex;align-items:center;gap:8px">{{ $slide['cta_primary_text'] }} <span class="material-symbols-outlined">arrow_forward</span></a>
                    @endif
                    @if(!empty($slide['cta_secondary_text']))
                    <a href="{{ $slide['cta_secondary_link'] ?? '#' }}" style="display:inline-block;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.25)" class="text-white px-8 py-4 rounded-xl font-headline-sm">{{ $slide['cta_secondary_text'] }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <button type="button" id="hs-prev" class="hs-btn-nav" style="left:1rem"><span class="material-symbols-outlined">chevron_left</span></button>
    <button type="button" id="hs-next" class="hs-btn-nav" style="right:1rem"><span class="material-symbols-outlined">chevron_right</span></button>
    <div id="hs-dot-wrap"></div>
</section>
@endif

@script
<script>
(function(){
    var slides  = document.querySelectorAll('#hs .hs-slide');
    var dotWrap = document.getElementById('hs-dot-wrap');
    var cur     = 0;
    var n       = slides.length;
    var timer;
    if(!n || !dotWrap) return;

    for(var i=0;i<n;i++){
        var d=document.createElement('button');
        d.type='button';
        d.className='hs-dot';
        d.style.width='8px';
        d.setAttribute('data-i',i);
        d.addEventListener('click',function(){go(+this.getAttribute('data-i'));});
        dotWrap.appendChild(d);
    }

    function updateDots(){
        var ds=dotWrap.querySelectorAll('.hs-dot');
        ds.forEach(function(d,i){
            if(i===cur){d.classList.add('on');d.style.width='32px';}
            else{d.classList.remove('on');d.style.width='8px';}
        });
    }

    function go(idx){
        slides[cur].classList.remove('on');
        cur=(idx%n+n)%n;
        slides[cur].classList.add('on');
        updateDots();
        clearInterval(timer);
        timer=setInterval(function(){go(cur+1);},5000);
    }

    document.getElementById('hs-prev').addEventListener('click',function(){go(cur-1);});
    document.getElementById('hs-next').addEventListener('click',function(){go(cur+1);});

    updateDots();
    timer=setInterval(function(){go(cur+1);},5000);
})();
</script>
@endscript

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
<span class="material-symbols-outlined text-[180px] text-primary relative">eco</span>
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
<button type="button" class="font-label-bold flex items-center gap-2 group-hover:gap-4 transition-all {{ $audCtaColor[$ci] ?? '' }}">
{{ $card['cta_text'] ?? '' }} <span class="material-symbols-outlined">east</span>
</button>
</div>
@endforeach
</div>
</section>
@endif

{{-- ═══════════════════════════════════════ LE GUICHET ═══════════════════════════════════════ --}}
@if($guichetSection?->is_active !== false)
<section class="py-20 bg-primary text-on-primary overflow-hidden relative" id="guichet">
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
<div class="flex-shrink-0 w-full md:w-1/3 text-center">
<span class="material-symbols-outlined text-[160px] text-primary/10">event_available</span>
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

{{-- ══════════════════════════════════ MÉDIATHÈQUE ══════════════════════════════════════════ --}}
@if($mediaSection?->is_active !== false && count($mediaPhotos))
<section class="py-20 bg-surface-container-low" id="mediatheque">
<div class="px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
    <div class="text-center mb-12" data-animate="fade-up">
        <h2 class="font-headline-lg text-headline-lg text-primary mb-4 uppercase">{{ $mediaTitle }}</h2>
        <div class="h-1.5 w-24 bg-secondary mx-auto rounded-full"></div>
        <p class="font-body-lg text-on-surface-variant mt-6 max-w-xl mx-auto">{{ $mediaDesc }}</p>
    </div>

    <div class="flex justify-center gap-2 mb-10 flex-wrap">
        <button type="button" onclick="filterMedia('all', this)" class="media-tab px-5 py-2 rounded-full font-label-bold text-sm bg-primary text-on-primary transition-all">Tout</button>
        <button type="button" onclick="filterMedia('terrain', this)" class="media-tab px-5 py-2 rounded-full font-label-bold text-sm bg-surface border border-outline-variant text-on-surface-variant hover:bg-primary hover:text-on-primary transition-all">Terrain</button>
        <button type="button" onclick="filterMedia('formation', this)" class="media-tab px-5 py-2 rounded-full font-label-bold text-sm bg-surface border border-outline-variant text-on-surface-variant hover:bg-primary hover:text-on-primary transition-all">Formation</button>
        <button type="button" onclick="filterMedia('evenement', this)" class="media-tab px-5 py-2 rounded-full font-label-bold text-sm bg-surface border border-outline-variant text-on-surface-variant hover:bg-primary hover:text-on-primary transition-all">Événements</button>
    </div>

    <div id="media-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($mediaPhotos as $photo)
        <div class="media-item" data-cat="{{ $photo['category'] ?? 'terrain' }}" data-animate="zoom-in" data-delay="{{ ($loop->index % 4) * 100 }}">
            <div class="relative overflow-hidden rounded-2xl aspect-square bg-surface-container-high group cursor-pointer">
                <img src="{{ $photo['src'] ?? '' }}" alt="{{ $photo['alt'] ?? '' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-4xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">zoom_in</span>
                </div>
                <span class="absolute bottom-2 left-2 {{ $catColors[$photo['category'] ?? 'terrain'] ?? 'bg-primary text-on-primary' }} text-xs px-2 py-1 rounded-full font-label-bold">
                    {{ $catLabels[$photo['category'] ?? 'terrain'] ?? 'Photo' }}
                </span>
            </div>
        </div>
        @endforeach
    </div>
</div>
</section>
@endif

@script
<script>
function filterMedia(cat, btn) {
    document.querySelectorAll('.media-tab').forEach(function(t) {
        t.className = 'media-tab px-5 py-2 rounded-full font-label-bold text-sm bg-surface border border-outline-variant text-on-surface-variant hover:bg-primary hover:text-on-primary transition-all';
    });
    btn.className = 'media-tab px-5 py-2 rounded-full font-label-bold text-sm bg-primary text-on-primary transition-all';
    document.querySelectorAll('.media-item').forEach(function(item) {
        if (cat === 'all' || item.getAttribute('data-cat') === cat) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>
@endscript

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
            <p class="text-on-surface-variant font-body-sm">Facilitateur d'avenir pour l'agroécologie au Burkina Faso. Connecter les compétences d'aujourd'hui aux défis de demain.</p>
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
                    <li><a class="hover:text-primary" href="#contact">Contact</a></li>
                </ul>
            </div>
            <div>
                <h6 class="font-label-bold text-on-surface mb-4">Légal</h6>
                <ul class="space-y-2 text-body-sm text-on-surface-variant">
                    <li><a class="hover:text-primary" href="#">Mentions Légales</a></li>
                    <li><a class="hover:text-primary" href="#">Confidentialité</a></li>
                </ul>
            </div>
        </div>


    </div>

    {{-- Barre de copyright --}}
    <div class="border-t border-outline-variant/30 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
        <p class="text-body-sm text-on-surface-variant">© {{ date('Y') }} Agro Eco BAARA. Tous droits réservés.</p>
        <div class="flex gap-6">
            @if($autresFbLink && $autresFbLink !== '#')
            <a class="text-on-surface-variant hover:text-primary" href="{{ $autresFbLink }}" target="_blank">
                <span class="material-symbols-outlined">facebook</span>
            </a>
            @endif
            <a class="text-on-surface-variant hover:text-primary" href="#">
                <span class="material-symbols-outlined">linked_camera</span>
            </a>
            <a class="text-on-surface-variant hover:text-primary" href="#">
                <span class="material-symbols-outlined">share</span>
            </a>
        </div>
    </div>

</div>
</footer>
</div>
