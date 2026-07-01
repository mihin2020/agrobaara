<div class="space-y-6">

    {{-- En-tête --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Messages de contact</h2>
            <p class="text-[#41493b] mt-1 text-sm">Messages reçus depuis le formulaire du site public.</p>
        </div>
        @if($unreadCount > 0)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#2c6904]/10 text-[#2c6904] rounded-full text-xs font-bold">
                <span class="material-symbols-outlined text-sm">mark_email_unread</span>
                {{ $unreadCount }} non-lu{{ $unreadCount > 1 ? 's' : '' }}
            </span>
        @endif
    </div>

    {{-- Filtres --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-lg">search</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Rechercher par nom, email ou contenu..."
                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
        </div>
        <select wire:model.live="filter"
                class="px-4 py-2.5 bg-white border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]">
            <option value="all">Tous</option>
            <option value="unread">Non-lus</option>
            <option value="read">Lus</option>
        </select>
    </div>

    {{-- Contenu principal --}}
    <div class="flex gap-6">

        {{-- Liste des messages --}}
        <div class="flex-1 space-y-2">
            @forelse($messages as $msg)
                <button wire:click="selectMessage('{{ $msg->id }}')" type="button"
                        class="w-full text-left p-4 rounded-xl border transition-all
                            {{ $selectedId === $msg->id ? 'bg-[#2c6904]/5 border-[#2c6904]/30 shadow-sm' : ($msg->is_read ? 'bg-white border-[#c1c9b6] hover:border-[#2c6904]/30' : 'bg-[#f5f9f2] border-[#2c6904]/20 hover:border-[#2c6904]/40') }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                @if(!$msg->is_read)
                                    <span class="w-2 h-2 rounded-full bg-[#2c6904] flex-shrink-0"></span>
                                @endif
                                <p class="font-semibold text-sm text-[#1e1b18] truncate {{ !$msg->is_read ? 'font-bold' : '' }}">{{ $msg->full_name }}</p>
                            </div>
                            <p class="text-xs text-[#717a69] mt-0.5">{{ $msg->email }}</p>
                            <p class="text-xs text-[#41493b] mt-1 line-clamp-2">{{ Str::limit($msg->message, 100) }}</p>
                        </div>
                        <span class="text-[10px] text-[#717a69] flex-shrink-0 mt-0.5">{{ $msg->created_at->diffForHumans() }}</span>
                    </div>
                </button>
            @empty
                <div class="text-center py-12 text-[#717a69]">
                    <span class="material-symbols-outlined text-4xl mb-2">inbox</span>
                    <p class="text-sm">Aucun message trouvé.</p>
                </div>
            @endforelse

            <div class="mt-4">
                {{ $messages->links() }}
            </div>
        </div>

        {{-- Détail du message sélectionné --}}
        @if($selected)
            <div class="hidden lg:block w-[420px] flex-shrink-0">
                <div class="bg-white rounded-2xl border border-[#c1c9b6] overflow-hidden sticky top-24">
                    <div class="px-5 py-4 border-b border-[#c1c9b6] bg-[#fbf2ed] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base text-[#2c6904]">email</span>
                            <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Message</h3>
                        </div>
                        <div class="flex items-center gap-1">
                            <button wire:click="deleteMessage('{{ $selected->id }}')"
                                    wire:confirm="Supprimer ce message définitivement ?"
                                    class="p-1.5 text-[#717a69] hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                            <button wire:click="closeMessage"
                                    class="p-1.5 text-[#717a69] hover:text-[#1e1b18] hover:bg-[#f5ece7] rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-base">close</span>
                            </button>
                        </div>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm text-[#2c6904]">person</span>
                                <span class="text-sm font-semibold text-[#1e1b18]">{{ $selected->full_name }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm text-[#717a69]">mail</span>
                                <a href="mailto:{{ $selected->email }}" class="text-sm text-[#2c6904] hover:underline">{{ $selected->email }}</a>
                            </div>
                            @if($selected->phone)
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm text-[#717a69]">phone</span>
                                    <span class="text-sm text-[#41493b]">{{ $selected->phone }}</span>
                                </div>
                            @endif
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm text-[#717a69]">schedule</span>
                                <span class="text-xs text-[#717a69]">{{ $selected->created_at->format('d/m/Y à H:i') }}</span>
                            </div>
                        </div>
                        <hr class="border-[#c1c9b6]" />
                        <div class="text-sm text-[#1e1b18] leading-relaxed whitespace-pre-wrap bg-[#fbf2ed] rounded-xl p-4">{{ $selected->message }}</div>
                        @if($selected->ip_address)
                            <p class="text-[10px] text-[#717a69]">IP : {{ $selected->ip_address }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
