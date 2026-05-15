@if ($paginator->hasPages())
    <nav class="flex items-center gap-1">
        {{-- Précédent --}}
        @if ($paginator->onFirstPage())
            <span class="p-1.5 text-[#c1c9b6] rounded-lg cursor-not-allowed">
                <span class="material-symbols-outlined text-xl">chevron_left</span>
            </span>
        @else
            <button wire:click="previousPage" wire:loading.attr="disabled"
                    class="p-1.5 text-[#41493b] hover:bg-[#e9e1dc] rounded-lg transition-colors">
                <span class="material-symbols-outlined text-xl">chevron_left</span>
            </button>
        @endif

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 text-[#717a69] text-sm">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-9 h-9 bg-[#2c6904] text-white rounded-xl font-bold text-sm flex items-center justify-center">
                            {{ $page }}
                        </span>
                    @else
                        <button wire:click="gotoPage({{ $page }})"
                                class="w-9 h-9 hover:bg-[#e9e1dc] text-[#41493b] rounded-xl font-semibold text-sm flex items-center justify-center transition-colors">
                            {{ $page }}
                        </button>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Suivant --}}
        @if ($paginator->hasMorePages())
            <button wire:click="nextPage" wire:loading.attr="disabled"
                    class="p-1.5 text-[#41493b] hover:bg-[#e9e1dc] rounded-lg transition-colors">
                <span class="material-symbols-outlined text-xl">chevron_right</span>
            </button>
        @else
            <span class="p-1.5 text-[#c1c9b6] rounded-lg cursor-not-allowed">
                <span class="material-symbols-outlined text-xl">chevron_right</span>
            </span>
        @endif
    </nav>
@endif
