<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Agro Eco BAARA' }}</title>
    <meta name="description" content="Plateforme de mise en relation pour l'emploi agroécologique au Burkina Faso." />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary-fixed-dim": "#93d86c",
                        "internal-section-bg": "#F9F7F2",
                        "tertiary-container": "#7b745e",
                        "surface": "#fff8f5",
                        "outline-variant": "#c1c9b6",
                        "surface-container-lowest": "#ffffff",
                        "background": "#fff8f5",
                        "tertiary": "#615c47",
                        "surface-container-low": "#fbf2ed",
                        "on-tertiary-fixed": "#1f1c0b",
                        "surface-dim": "#e1d8d4",
                        "on-secondary": "#ffffff",
                        "status-locked": "#F57C00",
                        "error-container": "#ffdad6",
                        "outline": "#717a69",
                        "surface-bright": "#fff8f5",
                        "on-surface-variant": "#41493b",
                        "on-background": "#1e1b18",
                        "on-primary-container": "#f8ffed",
                        "status-accepted": "#2E7D32",
                        "on-error-container": "#93000a",
                        "secondary-fixed-dim": "#ffb870",
                        "on-tertiary-container": "#fffbff",
                        "on-primary-fixed-variant": "#1f5100",
                        "status-refused": "#D32F2F",
                        "status-draft": "#757575",
                        "surface-container-highest": "#e9e1dc",
                        "surface-variant": "#e9e1dc",
                        "on-tertiary": "#ffffff",
                        "secondary-container": "#ffb870",
                        "primary-container": "#448322",
                        "on-tertiary-fixed-variant": "#4c4733",
                        "inverse-primary": "#93d86c",
                        "inverse-surface": "#34302c",
                        "primary": "#2c6904",
                        "on-primary-fixed": "#082100",
                        "on-primary": "#ffffff",
                        "inverse-on-surface": "#f8efea",
                        "tertiary-fixed-dim": "#cec6ad",
                        "surface-tint": "#2e6c08",
                        "surface-container-high": "#efe6e2",
                        "primary-fixed": "#aef585",
                        "on-secondary-fixed-variant": "#693c00",
                        "on-secondary-fixed": "#2c1600",
                        "on-secondary-container": "#794704",
                        "on-error": "#ffffff",
                        "secondary-fixed": "#ffdcbd",
                        "on-surface": "#1e1b18",
                        "land-green-vibrant": "#76B82A",
                        "surface-container": "#f5ece7",
                        "secondary": "#875212",
                        "error": "#ba1a1a",
                        "tertiary-fixed": "#ebe2c8"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "margin-desktop": "64px",
                        "form-gap": "20px",
                        "gutter-desktop": "24px",
                        "unit": "4px",
                        "gutter-mobile": "16px",
                        "margin-mobile": "16px",
                        "container-max": "1280px"
                    },
                    "fontFamily": {
                        "label-bold": ["Inter"],
                        "headline-lg": ["Sora"],
                        "body-md": ["Inter"],
                        "body-sm": ["Inter"],
                        "body-lg": ["Inter"],
                        "code-ref": ["JetBrains Mono"],
                        "display-hero-mobile": ["Sora"],
                        "headline-sm": ["Sora"],
                        "headline-md": ["Sora"],
                        "display-hero": ["Sora"]
                    },
                    "fontSize": {
                        "label-bold": ["14px", {"lineHeight": "16px", "fontWeight": "600"}],
                        "headline-lg": ["32px", {"lineHeight": "40px", "fontWeight": "600"}],
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                        "code-ref": ["14px", {"lineHeight": "20px", "fontWeight": "500"}],
                        "display-hero-mobile": ["32px", {"lineHeight": "40px", "fontWeight": "700"}],
                        "headline-sm": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                        "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                        "display-hero": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700"}]
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .bento-item { transition: transform 0.3s ease; }
        .bento-item:hover { transform: translateY(-4px); }
        details > summary { list-style: none; }
        details > summary::-webkit-details-marker { display: none; }
        details[open] summary .expand-icon { transform: rotate(180deg); }

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
