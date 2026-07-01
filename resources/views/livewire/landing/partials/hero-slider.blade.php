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
