<div class="space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-base">check_circle</span>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-base">error</span>{{ session('error') }}
        </div>
    @endif

    {{-- En-tête --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Gestion des Utilisateurs</h2>
            <p class="text-[#41493b] mt-1 text-sm">Comptes d'accès au back-office BAARA.</p>
        </div>
        @can('super_admin', \App\Models\User::class)
            <a href="{{ route('admin.admin.users.create') }}" wire:navigate
               class="flex items-center gap-2 bg-[#1e1b18] text-white py-2.5 px-5 rounded-xl font-semibold text-sm shadow-sm hover:opacity-90 transition-opacity">
                <span class="material-symbols-outlined text-lg">person_add</span>
                Nouvel utilisateur
            </a>
        @endcan
    </div>

    {{-- Recherche --}}
    <div class="bg-white p-4 rounded-2xl border border-[#c1c9b6] shadow-sm">
        <div class="relative max-w-md">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-lg">search</span>
            <input wire:model.live.debounce.400ms="search"
                   type="text"
                   placeholder="Nom, prénom, e-mail..."
                   class="w-full pl-10 pr-4 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1e1b18]/20 focus:border-[#1e1b18] transition-all" />
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white border border-[#c1c9b6] rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[#fbf2ed] border-b border-[#c1c9b6]">
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider">Utilisateur</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden md:table-cell">E-mail</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden md:table-cell">Rôle</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden lg:table-cell">Créé le</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c1c9b6]/40">
                    @forelse($users as $user)
                        <tr class="hover:bg-[#fbf2ed]/50 transition-colors">
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-[#1e1b18] flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-[#1e1b18]">{{ $user->full_name }}</p>
                                        <p class="text-xs text-[#41493b] md:hidden">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <p class="text-sm text-[#41493b]">{{ $user->email }}</p>
                            </td>
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                @foreach($user->roles as $role)
                                    <span class="text-xs font-semibold px-2 py-0.5 bg-[#f5ece7] text-[#1e1b18] rounded-lg border border-[#c1c9b6]">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td class="px-4 py-3.5">
                                @php
                                    $statusColors = [
                                        'active'           => 'bg-green-100 text-green-800',
                                        'inactive'         => 'bg-gray-100 text-gray-600',
                                        'locked'           => 'bg-orange-100 text-orange-800',
                                        'pending_password' => 'bg-blue-100 text-blue-800',
                                    ];
                                @endphp
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $statusColors[$user->status->value] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $user->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 hidden lg:table-cell">
                                <p class="text-xs text-[#717a69]">{{ $user->created_at->format('d/m/Y') }}</p>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    {{-- Toggle actif/inactif --}}
                                    @if($user->id !== auth()->id())
                                        <button wire:click="toggleStatus('{{ $user->id }}')"
                                                class="p-1.5 text-[#41493b] hover:bg-[#f5ece7] rounded-lg transition-colors"
                                                title="{{ $user->status->value === 'active' ? 'Désactiver' : 'Activer' }}">
                                            <span class="material-symbols-outlined text-lg">
                                                {{ $user->status->value === 'active' ? 'toggle_on' : 'toggle_off' }}
                                            </span>
                                        </button>
                                    @endif
                                    {{-- Déverrouiller --}}
                                    @if($user->status->value === 'locked')
                                        <button wire:click="unlockUser('{{ $user->id }}')"
                                                class="p-1.5 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors"
                                                title="Déverrouiller le compte">
                                            <span class="material-symbols-outlined text-lg">lock_open</span>
                                        </button>
                                    @endif
                                    {{-- Supprimer --}}
                                    @if($user->id !== auth()->id())
                                        <button wire:click="confirmDelete('{{ $user->id }}')"
                                                class="p-1.5 text-[#41493b] hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors"
                                                title="Supprimer">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <span class="material-symbols-outlined text-5xl text-[#c1c9b6] block mb-3">manage_accounts</span>
                                <p class="text-[#717a69] font-medium text-sm">Aucun utilisateur trouvé.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-4 py-3.5 flex flex-col sm:flex-row items-center justify-between gap-3 bg-[#fbf2ed] border-t border-[#c1c9b6]">
                <p class="text-xs text-[#41493b]">
                    {{ $users->total() }} utilisateur(s) au total
                </p>
                {{ $users->links('livewire.partials.pagination') }}
            </div>
        @endif
    </div>

    {{-- Modal confirmation suppression --}}
    @if($confirmingDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
             x-data x-init="$el.focus()" @keydown.escape.window="$wire.set('confirmingDelete', false)">
            <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-red-600">warning</span>
                    </div>
                    <h3 class="font-sora font-bold text-base text-[#1e1b18]">Confirmer la suppression</h3>
                </div>
                <p class="text-sm text-[#41493b] mb-6">Cette action est irréversible. L'utilisateur sera définitivement supprimé.</p>
                <div class="flex justify-end gap-3">
                    <button wire:click="$set('confirmingDelete', false)"
                            class="px-4 py-2 border border-[#c1c9b6] text-[#41493b] font-semibold rounded-xl hover:bg-[#f5ece7] transition-colors text-sm">
                        Annuler
                    </button>
                    <button wire:click="deleteUser"
                            class="px-4 py-2 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition-colors text-sm">
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
