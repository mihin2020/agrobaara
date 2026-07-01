<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Agro Eco BAARA - Guichet emploi agroécologique au Burkina Faso' }}</title>
    <link rel="icon" type="image/jpeg" href="/images/logo.jpeg" />
    <meta name="description" content="Agro Eco BAARA : plateforme de mise en relation entre jeunes talents et entreprises de l'agroécologie au Burkina Faso. Emploi, formation, insertion professionnelle." />
    <meta name="keywords" content="agroécologie, emploi, Burkina Faso, jeunes, insertion professionnelle, agriculture durable, Ouagadougou" />
    <meta name="robots" content="index, follow" />
    <link rel="canonical" href="{{ url()->current() }}" />

    {{-- Open Graph --}}
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ $title ?? 'Agro Eco BAARA - Guichet emploi agroécologique' }}" />
    <meta property="og:description" content="Plateforme de mise en relation pour l'emploi agroécologique au Burkina Faso." />
    <meta property="og:image" content="{{ asset('images/logo.jpeg') }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:locale" content="fr_BF" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Raleway:wght@600;700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ── Scroll animations ──────────────────────────────────── */
        [data-animate] {
            opacity: 0;
            transition: opacity .65s cubic-bezier(.22,1,.36,1),
                        transform .65s cubic-bezier(.22,1,.36,1);
            will-change: opacity, transform;
        }
        [data-animate="fade-up"]    { transform: translateY(48px); }
        [data-animate="fade-down"]  { transform: translateY(-48px); }
        [data-animate="fade-left"]  { transform: translateX(-56px); }
        [data-animate="fade-right"] { transform: translateX(56px); }
        [data-animate="zoom-in"]    { transform: scale(.88); }
        [data-animate="flip-up"]    { transform: perspective(600px) rotateX(12deg) translateY(32px); }

        [data-animate].anim-in {
            opacity: 1;
            transform: none;
        }

        /* Delays */
        [data-delay="100"] { transition-delay: .10s; }
        [data-delay="150"] { transition-delay: .15s; }
        [data-delay="200"] { transition-delay: .20s; }
        [data-delay="300"] { transition-delay: .30s; }
        [data-delay="400"] { transition-delay: .40s; }
        [data-delay="500"] { transition-delay: .50s; }
        [data-delay="600"] { transition-delay: .60s; }
        [data-delay="700"] { transition-delay: .70s; }
        [data-delay="800"] { transition-delay: .80s; }

        /* Hover lift sur les cards */
        .card-lift {
            transition: transform .3s cubic-bezier(.22,1,.36,1), box-shadow .3s ease;
        }
        .card-lift:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px -12px rgba(44,105,4,.18);
        }

        /* Underline animé sur les liens nav */
        nav a { position: relative; }
        nav a::after {
            content: '';
            position: absolute;
            bottom: -2px; left: 0;
            width: 0; height: 2px;
            background: currentColor;
            transition: width .25s ease;
        }
        nav a:hover::after { width: 100%; }
        /* Justifier les textes de l'espace public */
        p, .prose p, .font-body-md, .font-body-lg { text-align: justify; }
    </style>
    @livewireStyles
</head>
<body class="bg-surface text-on-surface font-body-md selection:bg-primary-container selection:text-on-primary-container">

    {{ $slot }}

    @livewireScripts
    <script>
    (function () {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('anim-in');
                    observer.unobserve(e.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

        function observeAll() {
            document.querySelectorAll('[data-animate]').forEach(el => {
                if (!el.classList.contains('anim-in')) observer.observe(el);
            });
        }

        // Initial pass
        observeAll();

        // Re-observe after Livewire re-renders (morphing can add new elements)
        document.addEventListener('livewire:navigated', observeAll);
        document.addEventListener('livewire:updated',   observeAll);
    })();
    </script>
</body>
</html>
