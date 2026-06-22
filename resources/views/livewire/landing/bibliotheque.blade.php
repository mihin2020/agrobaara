<div x-data="{
    open: false,
    title: '',
    url: '',
    isPdf: false,
    show(doc) {
        this.title = doc.title;
        this.url = doc.url;
        this.isPdf = doc.isPdf;
        this.open = true;
        document.body.style.overflow = 'hidden';
    },
    close() {
        this.open = false;
        this.url = '';
        document.body.style.overflow = '';
    }
}" @keydown.escape.window="close()">

<style>
.library-bg {
    background:
        linear-gradient(180deg, rgba(255,248,245,0.92) 0%, rgba(251,242,237,0.96) 100%),
        repeating-linear-gradient(90deg, transparent, transparent 80px, rgba(135,82,18,0.03) 80px, rgba(135,82,18,0.03) 81px);
}
.library-shelf {
    background: linear-gradient(180deg, #a67c52 0%, #875212 45%, #6b3f0e 100%);
    box-shadow: 0 8px 20px rgba(107,63,14,0.35), inset 0 2px 0 rgba(255,255,255,0.15);
    border-radius: 4px;
    height: 14px;
}
.book-card {
    perspective: 800px;
}
.book-cover-wrap {
    aspect-ratio: 3/4;
    transform-style: preserve-3d;
    transition: transform 0.35s cubic-bezier(.22,1,.36,1), box-shadow 0.35s ease;
    box-shadow:
        4px 4px 12px rgba(30,27,24,0.18),
        inset -3px 0 8px rgba(0,0,0,0.08);
}
.book-card:hover .book-cover-wrap {
    transform: rotateY(-8deg) translateY(-10px) scale(1.03);
    box-shadow:
        12px 16px 28px rgba(30,27,24,0.28),
        inset -3px 0 8px rgba(0,0,0,0.08);
}
.book-spine {
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 8px;
    background: linear-gradient(90deg, rgba(0,0,0,0.25), rgba(255,255,255,0.08));
    z-index: 2;
}
.book-cover-img, .book-cover-wrap canvas {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.book-cover-fallback {
    background: linear-gradient(145deg, #448322 0%, #2c6904 50%, #1f5100 100%);
}
.pdf-cover-loading {
    background: linear-gradient(110deg, #f5ece7 8%, #ebe2c8 18%, #f5ece7 33%);
    background-size: 200% 100%;
    animation: shimmer 1.4s linear infinite;
}
@keyframes shimmer {
    to { background-position-x: -200%; }
}
</style>

<header class="bg-surface docked full-width top-0 border-b border-outline-variant z-50 sticky">
    <nav class="flex justify-between items-center w-full px-margin-mobile md:px-margin-desktop py-4 max-w-container-max mx-auto">
        <a href="{{ route('home') }}" class="flex items-center gap-4">
            <img alt="Agro Eco BAARA Logo" class="h-20 w-auto object-contain" src="{{ $heroLogo }}" />
        </a>
        <div class="hidden md:flex items-center gap-8">
            <a class="text-on-surface-variant font-label-bold hover:text-primary transition-colors" href="{{ route('home') }}">Accueil</a>
            <a class="text-on-surface-variant font-label-bold hover:text-primary transition-colors" href="{{ route('home') }}#audiences">S'adresse à vous</a>
            <a class="text-on-surface-variant font-label-bold hover:text-primary transition-colors" href="{{ route('home') }}#guichet">Le Guichet</a>
            <a class="text-on-surface-variant font-label-bold hover:text-primary transition-colors" href="{{ route('home') }}#partenaires">Partenaires</a>
            <a class="text-on-surface-variant font-label-bold hover:text-primary transition-colors" href="{{ route('home') }}#mediatheque">Médiathèque</a>
            <a class="text-primary border-b-2 border-primary pb-1 font-label-bold" href="{{ route('bibliotheque') }}">Bibliothèque</a>
            <a class="text-on-surface-variant font-label-bold hover:text-primary transition-colors" href="{{ route('home') }}#contact">Contact</a>
            <a href="{{ route('login') }}" class="bg-primary text-on-primary px-6 py-2 rounded-lg font-label-bold hover:opacity-90 transition-all">Connexion</a>
        </div>
        <a href="{{ route('home') }}" class="md:hidden text-primary">
            <span class="material-symbols-outlined text-3xl">home</span>
        </a>
    </nav>
</header>

<main class="library-bg min-h-screen py-16 px-margin-mobile md:px-margin-desktop">
    <div class="max-w-container-max mx-auto">
        <div class="text-center mb-14" data-animate="fade-up">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-secondary/15 text-secondary rounded-full font-label-bold text-sm mb-5">
                <span class="material-symbols-outlined text-base">local_library</span>
                Espace documentaire
            </div>
            <h1 class="font-headline-lg text-headline-lg text-primary mb-4 uppercase">Bibliothèque</h1>
            <div class="h-1.5 w-24 bg-secondary mx-auto rounded-full"></div>
            <p class="font-body-lg text-on-surface-variant mt-6 max-w-2xl mx-auto">
                Parcourez notre collection de rapports, guides et ressources — comme dans une vraie bibliothèque.
            </p>
        </div>

        @if($documents->isEmpty())
            <div class="bg-surface-container-low rounded-3xl border border-outline-variant p-14 text-center shadow-inner">
                <span class="material-symbols-outlined text-6xl text-outline-variant mb-4 block">shelves</span>
                <p class="font-headline-sm text-on-surface-variant">Les étagères sont vides pour le moment.</p>
            </div>
        @else
            @php $chunks = $documents->chunk(4); @endphp
            <div class="space-y-2">
                @foreach($chunks as $rowIndex => $row)
                <div class="relative pt-4 pb-10">
                    {{-- Livres sur l'étagère --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6 md:gap-8 px-2 md:px-6 items-end">
                        @foreach($row as $doc)
                            @php $url = $doc->publicUrl(); @endphp
                            <button type="button"
                                    class="book-card text-left group"
                                    @click="show({ title: @js($doc->title), url: @js($url), isPdf: @js($doc->canEmbed()) })">
                                <div class="book-cover-wrap relative rounded-r-md rounded-l-sm overflow-hidden bg-surface-container-high">
                                    <div class="book-spine"></div>
                                    @if($doc->coverUrl())
                                        <img src="{{ $doc->coverUrl() }}" alt="{{ $doc->title }}" class="book-cover-img" loading="lazy" />
                                    @elseif($doc->usesPdfFirstPageAsCover())
                                        <div class="w-full h-full pdf-cover-loading relative">
                                            <canvas data-pdf-cover="{{ $url }}" class="book-cover-img absolute inset-0 w-full h-full"></canvas>
                                            <div class="pdf-cover-fallback absolute inset-0 hidden flex-col items-center justify-center book-cover-fallback text-white p-4 text-center">
                                                <span class="material-symbols-outlined text-4xl mb-2 opacity-80">picture_as_pdf</span>
                                                <span class="text-xs font-label-bold uppercase tracking-wide opacity-90">PDF</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-full h-full book-cover-fallback flex flex-col items-center justify-center text-white p-4 text-center">
                                            <span class="material-symbols-outlined text-5xl mb-3 opacity-90">
                                                {{ $doc->isPdf() ? 'picture_as_pdf' : ($doc->type === 'file' ? 'menu_book' : 'link') }}
                                            </span>
                                            <span class="text-[10px] font-label-bold uppercase tracking-widest opacity-80">Document</span>
                                        </div>
                                    @endif
                                    {{-- Reflet --}}
                                    <div class="absolute inset-0 bg-gradient-to-tr from-black/10 via-transparent to-white/10 pointer-events-none"></div>
                                </div>
                                <div class="mt-4 px-1">
                                    <h2 class="font-headline-sm text-on-surface text-sm leading-snug line-clamp-2 group-hover:text-primary transition-colors">{{ $doc->title }}</h2>
                                    @if($doc->description)
                                        <p class="text-xs text-on-surface-variant mt-1 line-clamp-2">{{ $doc->description }}</p>
                                    @endif
                                    <p class="text-[11px] text-primary font-label-bold mt-2 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        {{ $doc->canEmbed() ? 'Lire' : 'Ouvrir' }}
                                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                    </p>
                                </div>
                            </button>
                        @endforeach
                    </div>
                    {{-- Étagère en bois --}}
                    <div class="library-shelf absolute bottom-0 left-0 right-0 mx-2 md:mx-4"></div>
                    <div class="absolute bottom-0 left-4 right-4 h-3 bg-black/10 blur-md rounded-full translate-y-2"></div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</main>

{{-- Lecteur PDF --}}
<div x-show="open" x-cloak
     class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-8"
     style="background:rgba(30,27,24,0.82);backdrop-filter:blur(6px);">
    <div class="relative w-full max-w-5xl h-[85vh] bg-surface rounded-2xl shadow-2xl overflow-hidden flex flex-col"
         @click.outside="close()">
        <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant bg-surface-container-low">
            <div class="flex items-center gap-3 min-w-0">
                <span class="material-symbols-outlined text-primary flex-shrink-0">auto_stories</span>
                <h3 class="font-headline-sm text-on-surface truncate" x-text="title"></h3>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <a :href="url" target="_blank" rel="noopener"
                   class="hidden sm:flex items-center gap-1 px-3 py-1.5 text-sm font-label-bold text-primary hover:bg-primary-container/20 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-base">open_in_new</span>
                    Nouvel onglet
                </a>
                <button type="button" @click="close()"
                        class="p-2 text-on-surface-variant hover:bg-surface-container-high rounded-xl transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>
        <div class="flex-1 bg-[#525659]">
            <template x-if="isPdf">
                <iframe :src="url" class="w-full h-full border-0" title="Lecteur PDF"></iframe>
            </template>
            <template x-if="open && !isPdf">
                <div class="h-full flex flex-col items-center justify-center gap-4 p-8 text-center bg-surface-container-low">
                    <span class="material-symbols-outlined text-5xl text-outline">link</span>
                    <p class="text-on-surface-variant">Ce document s'ouvre dans un nouvel onglet.</p>
                    <a :href="url" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-on-primary rounded-xl font-label-bold hover:opacity-90 transition-all">
                        Ouvrir le document
                        <span class="material-symbols-outlined text-base">open_in_new</span>
                    </a>
                </div>
            </template>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', renderPublicPdfCovers);
document.addEventListener('livewire:navigated', renderPublicPdfCovers);

async function renderPublicPdfCovers() {
    if (typeof pdfjsLib === 'undefined') return;
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    for (const canvas of document.querySelectorAll('canvas[data-pdf-cover]:not([data-rendered])')) {
        const url = canvas.getAttribute('data-pdf-cover');
        if (!url) continue;
        canvas.setAttribute('data-rendered', '1');
        const wrap = canvas.closest('.pdf-cover-loading');
        try {
            const pdf = await pdfjsLib.getDocument(url).promise;
            const page = await pdf.getPage(1);
            const parent = canvas.parentElement;
            const width = parent?.clientWidth || 200;
            const viewport = page.getViewport({ scale: 1 });
            const scale = width / viewport.width;
            const scaled = page.getViewport({ scale });
            canvas.width = scaled.width;
            canvas.height = scaled.height;
            await page.render({ canvasContext: canvas.getContext('2d'), viewport: scaled }).promise;
            wrap?.classList.remove('pdf-cover-loading');
        } catch (e) {
            canvas.classList.add('hidden');
            wrap?.classList.remove('pdf-cover-loading');
            wrap?.querySelector('.pdf-cover-fallback')?.classList.remove('hidden');
            wrap?.querySelector('.pdf-cover-fallback')?.classList.add('flex');
        }
    }
}
</script>

</div>
