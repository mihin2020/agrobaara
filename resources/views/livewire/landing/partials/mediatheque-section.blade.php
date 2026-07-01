{{-- ══════════════════════════════════ MÉDIATHÈQUE ══════════════════════════════════════════ --}}
@if($mediaSection?->is_active !== false && count($mediaPhotos))
<section class="py-20 bg-surface-container-low scroll-mt-24" id="mediatheque">
<div class="px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
    <div class="text-center mb-10" data-animate="fade-up">
        <h2 class="font-headline-lg text-headline-lg text-primary mb-4 uppercase">{{ $mediaTitle }}</h2>
        <div class="h-1.5 w-24 bg-secondary mx-auto rounded-full"></div>
        <p class="font-body-lg text-on-surface-variant mt-6 max-w-xl mx-auto">{{ $mediaDesc }}</p>
    </div>

    {{-- Filtres : catégories + Vidéos en dernier --}}
    <div class="flex justify-center gap-2 mb-8 flex-wrap" data-animate="fade-up" data-delay="50">
        @foreach($mediaCategories as $cat)
        <button type="button" onclick="filterMedia('{{ $cat['key'] }}', this)" class="media-tab px-5 py-2 rounded-full font-label-bold text-sm {{ $loop->first ? 'bg-primary text-on-primary' : 'bg-surface border border-outline-variant text-on-surface-variant hover:bg-primary hover:text-on-primary' }} transition-all">{{ $cat['label'] }}</button>
        @endforeach
        @if($hasMediaVideos)
        <button type="button" onclick="filterMedia('__videos__', this)" class="media-tab px-5 py-2 rounded-full font-label-bold text-sm bg-surface border border-outline-variant text-on-surface-variant hover:bg-[#283593] hover:text-white transition-all">
            <span class="material-symbols-outlined text-sm align-middle mr-0.5">smart_display</span>Vidéos
        </button>
        @endif
    </div>

    {{-- Grille unifiée photos + vidéos --}}
    <div id="media-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($mediaPhotos as $item)
        @php
            $isVideo = ($item['type'] ?? 'image') === 'video';
            $itemSrc = $item['src'] ?? '';
            $isEmbedVideo = $isVideo && $itemSrc && \App\Support\MediaVideoUrl::isEmbeddable($itemSrc);
            $embedSrc = $isEmbedVideo ? \App\Support\MediaVideoUrl::embedUrl($itemSrc) : '';
            $thumbSrc = $isVideo ? \App\Support\MediaVideoUrl::thumbnailUrl($itemSrc) : '';
            $playSrc = $isEmbedVideo ? $embedSrc : $itemSrc;
        @endphp
        @php $itemCat = $item['category'] ?? 'terrain'; @endphp
        <div class="media-item" data-cat="{{ $itemCat }}" data-type="{{ $isVideo ? 'video' : 'photo' }}" data-animate="zoom-in" data-delay="{{ ($loop->index % 4) * 100 }}" @if($itemCat !== $firstCatKey) style="display:none" @endif>
            @if($isVideo && !empty($itemSrc))
            <button type="button"
                    class="media-video-trigger w-full text-left group"
                    data-embed="{{ $isEmbedVideo ? '1' : '0' }}"
                    data-src="{{ $playSrc }}"
                    data-title="{{ $item['alt'] ?? 'Vidéo' }}">
                <div class="relative overflow-hidden rounded-2xl aspect-square bg-[#1e1b18] shadow-sm">
                    @if($thumbSrc)
                    <img src="{{ $thumbSrc }}" alt="" class="w-full h-full object-cover opacity-90 group-hover:scale-105 transition-transform duration-500" loading="lazy" />
                    @else
                    <video src="{{ $itemSrc }}#t=0.1" class="w-full h-full object-cover pointer-events-none" muted playsinline preload="metadata"></video>
                    @endif
                    <div class="absolute inset-0 bg-black/25 group-hover:bg-black/40 transition-colors flex items-center justify-center">
                        <span class="w-12 h-12 rounded-full bg-white/95 text-[#283593] flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-2xl ml-0.5" style="font-variation-settings:'FILL' 1">play_arrow</span>
                        </span>
                    </div>
                    <span class="absolute bottom-2 left-2 bg-[#283593] text-white text-xs px-2 py-1 rounded-full font-label-bold">Vidéo</span>
                </div>
                @if(!empty($item['alt']))
                <p class="mt-2 text-xs text-on-surface-variant line-clamp-2 px-0.5">{{ $item['alt'] }}</p>
                @endif
            </button>
            @else
            <div class="relative overflow-hidden rounded-2xl aspect-square bg-surface-container-high group cursor-pointer"
                 @if(!empty($itemSrc)) onclick="openMediaLightbox('{{ $itemSrc }}', '{{ addslashes($item['alt'] ?? '') }}')" @endif>
                @if(!empty($itemSrc))
                <img src="{{ $itemSrc }}" alt="{{ $item['alt'] ?? '' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center pointer-events-none">
                    <span class="material-symbols-outlined text-white text-4xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">zoom_in</span>
                </div>
                @else
                <div class="w-full h-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-outline-variant text-4xl">broken_image</span>
                </div>
                @endif
                <span class="absolute bottom-2 left-2 {{ $catColors[$item['category'] ?? 'terrain'] ?? 'bg-primary text-on-primary' }} text-xs px-2 py-1 rounded-full font-label-bold">
                    {{ $catLabels[$item['category'] ?? 'terrain'] ?? 'Photo' }}
                </span>
            </div>
            @if(!empty($item['alt']))
            <p class="mt-2 text-xs text-on-surface-variant line-clamp-2 px-0.5">{{ $item['alt'] }}</p>
            @endif
            @endif
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
    if (btn) {
        btn.className = cat === '__videos__'
            ? 'media-tab px-5 py-2 rounded-full font-label-bold text-sm bg-[#283593] text-white transition-all'
            : 'media-tab px-5 py-2 rounded-full font-label-bold text-sm bg-primary text-on-primary transition-all';
    }
    document.querySelectorAll('#mediatheque .media-item').forEach(function(item) {
        var type = item.getAttribute('data-type');
        var itemCat = item.getAttribute('data-cat');
        var show = (cat === '__videos__' && type === 'video')
            || (cat !== '__videos__' && itemCat === cat);
        item.style.display = show ? '' : 'none';
    });
}

// Initialiser le filtre sur la première catégorie au chargement
document.addEventListener('DOMContentLoaded', function() {
    var firstTab = document.querySelector('.media-tab');
    if (firstTab) {
        filterMedia(firstTab.getAttribute('onclick').match(/'([^']+)'/)[1], firstTab);
    }
});

function openMediaLightbox(src, alt) {
    var overlay = document.getElementById('media-lightbox');
    var img = document.getElementById('media-lightbox-img');
    if (!overlay || !img) return;
    img.src = src;
    img.alt = alt || '';
    overlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeMediaLightbox() {
    var overlay = document.getElementById('media-lightbox');
    if (!overlay) return;
    overlay.classList.add('hidden');
    document.body.style.overflow = '';
}

function openVideoModal(isEmbed, src, title) {
    var overlay = document.getElementById('video-modal');
    var content = document.getElementById('video-modal-content');
    var titleEl = document.getElementById('video-modal-title');
    if (!overlay || !content || !src) return;
    content.innerHTML = '';
    if (isEmbed) {
        var iframe = document.createElement('iframe');
        iframe.src = src + (src.indexOf('?') >= 0 ? '&' : '?') + 'autoplay=1';
        iframe.className = 'w-full h-full border-0 rounded-xl';
        iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
        iframe.allowFullscreen = true;
        iframe.title = title || 'Vidéo';
        content.appendChild(iframe);
    } else {
        var video = document.createElement('video');
        video.src = src;
        video.controls = true;
        video.autoplay = true;
        video.playsInline = true;
        video.preload = 'auto';
        video.className = 'w-full h-full object-contain rounded-xl bg-black';
        content.appendChild(video);
        video.play().catch(function() {});
    }
    if (titleEl) titleEl.textContent = title || '';
    overlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeVideoModal() {
    var overlay = document.getElementById('video-modal');
    var content = document.getElementById('video-modal-content');
    if (!overlay) return;
    overlay.classList.add('hidden');
    if (content) content.innerHTML = '';
    document.body.style.overflow = '';
}

function initMediaVideoTriggers() {
    document.querySelectorAll('.media-video-trigger').forEach(function(btn) {
        if (btn.dataset.bound) return;
        btn.dataset.bound = '1';
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            openVideoModal(btn.dataset.embed === '1', btn.dataset.src || '', btn.dataset.title || 'Vidéo');
        });
    });
}

window.filterMedia = filterMedia;
window.openMediaLightbox = openMediaLightbox;
window.closeMediaLightbox = closeMediaLightbox;
window.openVideoModal = openVideoModal;
window.closeVideoModal = closeVideoModal;

initMediaVideoTriggers();
document.addEventListener('livewire:navigated', initMediaVideoTriggers);
document.addEventListener('livewire:updated', initMediaVideoTriggers);
</script>
@endscript

<div id="media-lightbox" class="hidden fixed inset-0 z-[200] bg-black/90 flex items-center justify-center p-4" onclick="closeMediaLightbox()">
    <button type="button" onclick="closeMediaLightbox()" class="absolute top-4 right-4 text-white p-2 rounded-full hover:bg-white/10">
        <span class="material-symbols-outlined text-3xl">close</span>
    </button>
    <img id="media-lightbox-img" src="" alt="" class="max-w-full max-h-[90vh] object-contain rounded-lg" onclick="event.stopPropagation()" />
</div>

<div id="video-modal" class="hidden fixed inset-0 z-[200] bg-black/92 flex flex-col items-center justify-center p-4 md:p-8" onclick="closeVideoModal()">
    <button type="button" onclick="closeVideoModal()" class="absolute top-4 right-4 text-white p-2 rounded-full hover:bg-white/10 z-10">
        <span class="material-symbols-outlined text-3xl">close</span>
    </button>
    <p id="video-modal-title" class="text-white font-label-bold text-base mb-4 max-w-4xl w-full text-center"></p>
    <div id="video-modal-content" class="w-full max-w-4xl aspect-video" onclick="event.stopPropagation()"></div>
</div>
