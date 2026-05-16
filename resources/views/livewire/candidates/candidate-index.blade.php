<div class="space-y-6">

    {{-- En-tête --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Gestion des Candidats</h2>
            <p class="text-[#41493b] mt-1 text-sm">Gérez et suivez les parcours des jeunes talents de l'agroécologie.</p>
        </div>
        @can('create', \App\Models\Candidate::class)
            <a href="{{ route('admin.candidates.create') }}" wire:navigate
               class="flex items-center gap-2 bg-[#2c6904] text-white py-2.5 px-5 rounded-xl font-semibold text-sm shadow-sm hover:bg-[#448322] transition-colors">
                <span class="material-symbols-outlined text-lg">person_add</span>
                Ajouter un candidat
            </a>
        @endcan
    </div>

    {{-- Filtres avancés --}}
    <div class="bg-white p-5 rounded-2xl border border-[#c1c9b6] shadow-sm">
        <div class="flex items-center gap-2 text-[#2c6904] font-bold text-xs uppercase tracking-widest mb-4">
            <span class="material-symbols-outlined text-base">filter_alt</span>
            Filtres Avancés
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">

            {{-- Recherche --}}
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Recherche</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#717a69] text-lg">search</span>
                    <input wire:model.live.debounce.400ms="search"
                           type="text"
                           placeholder="Nom, référence, téléphone..."
                           class="w-full pl-9 pr-4 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all" />
                </div>
            </div>

            {{-- Commune --}}
            <div>
                <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Commune</label>
                <select wire:model.live="commune"
                        class="w-full bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all">
                    <option value="">Toutes</option>
                    @foreach($communes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Compétence --}}
            <div>
                <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Compétence</label>
                <select wire:model.live="skill"
                        class="w-full bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all">
                    <option value="">Toutes</option>
                    @foreach($skills as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Niveau --}}
            <div>
                <label class="block text-xs font-semibold text-[#1e1b18] mb-1.5">Niveau d'étude</label>
                <select wire:model.live="education"
                        class="w-full bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all">
                    <option value="">Tous</option>
                    @foreach($educations as $e)
                        <option value="{{ $e['value'] }}">{{ $e['label'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Filtre sexe + reset --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mt-4 pt-4 border-t border-[#c1c9b6]/50">
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-[#1e1b18]">Sexe :</span>
                <div class="flex bg-[#fbf2ed] p-1 rounded-xl gap-0.5">
                    <button wire:click="$set('gender', '')"
                            @class(['px-3 py-1.5 text-xs font-semibold rounded-lg transition-all', 'bg-white text-[#2c6904] shadow-sm' => $gender === '', 'text-[#41493b] hover:text-[#1e1b18]' => $gender !== ''])>
                        Tous
                    </button>
                    <button wire:click="$set('gender', 'F')"
                            @class(['px-3 py-1.5 text-xs font-semibold rounded-lg transition-all', 'bg-white text-[#2c6904] shadow-sm' => $gender === 'F', 'text-[#41493b] hover:text-[#1e1b18]' => $gender !== 'F'])>
                        Femmes
                    </button>
                    <button wire:click="$set('gender', 'M')"
                            @class(['px-3 py-1.5 text-xs font-semibold rounded-lg transition-all', 'bg-white text-[#2c6904] shadow-sm' => $gender === 'M', 'text-[#41493b] hover:text-[#1e1b18]' => $gender !== 'M'])>
                        Hommes
                    </button>
                </div>
            </div>
            @if($search || $commune || $skill || $education || $gender)
                <button wire:click="resetFilters"
                        class="flex items-center gap-1 text-xs text-red-600 hover:text-red-700 font-semibold">
                    <span class="material-symbols-outlined text-base">filter_alt_off</span>
                    Effacer les filtres
                </button>
            @endif
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white border border-[#c1c9b6] rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[#fbf2ed] border-b border-[#c1c9b6]">
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider">Candidat</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider">Référence</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden md:table-cell">Commune</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden lg:table-cell">Compétences</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider hidden md:table-cell">Inscription</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#41493b] uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c1c9b6]/40">
                    @forelse($candidates as $candidate)
                        <tr class="hover:bg-[#fbf2ed]/50 transition-colors">
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-[#f5ece7] border border-[#c1c9b6] flex items-center justify-center font-bold text-sm text-[#2c6904] flex-shrink-0">
                                        {{ strtoupper(substr($candidate->first_name, 0, 1) . substr($candidate->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-[#1e1b18]">{{ $candidate->full_name }}</p>
                                        <p class="text-xs text-[#41493b]">{{ $candidate->age }} ans · {{ $candidate->gender->label() }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-mono text-xs font-semibold text-[#2c6904] bg-[#aef585]/20 px-2 py-1 rounded-lg">
                                    {{ $candidate->reference }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-sm text-[#1e1b18] hidden md:table-cell">
                                {{ $candidate->commune?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3.5 hidden lg:table-cell">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($candidate->skills->take(2) as $skill)
                                        <span class="px-2 py-0.5 bg-[#f5ece7] text-[#41493b] text-[11px] font-semibold rounded-lg border border-[#c1c9b6]">
                                            {{ $skill->name }}
                                        </span>
                                    @endforeach
                                    @if($candidate->skills->count() > 2)
                                        <span class="px-2 py-0.5 bg-[#f5ece7] text-[#717a69] text-[11px] font-semibold rounded-lg border border-[#c1c9b6]">
                                            +{{ $candidate->skills->count() - 2 }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-xs text-[#41493b] hidden md:table-cell">
                                {{ $candidate->created_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.candidates.show', $candidate) }}" wire:navigate
                                       class="p-1.5 text-[#41493b] hover:bg-[#f5ece7] hover:text-[#2c6904] rounded-lg transition-colors"
                                       title="Voir le profil">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                    </a>
                                    @can('update', $candidate)
                                        <a href="{{ route('admin.candidates.edit', $candidate) }}" wire:navigate
                                           class="p-1.5 text-[#41493b] hover:bg-[#f5ece7] hover:text-[#875212] rounded-lg transition-colors"
                                           title="Modifier">
                                            <span class="material-symbols-outlined text-lg">edit</span>
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <span class="material-symbols-outlined text-5xl text-[#c1c9b6] block mb-3">person_search</span>
                                <p class="text-[#717a69] font-medium">Aucun candidat trouvé.</p>
                                @if($search || $commune || $skill || $education || $gender)
                                    <button wire:click="resetFilters"
                                            class="mt-2 text-sm text-[#2c6904] hover:underline font-semibold">
                                        Effacer les filtres
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($candidates->hasPages())
            <div class="px-4 py-3.5 flex flex-col sm:flex-row items-center justify-between gap-3 bg-[#fbf2ed] border-t border-[#c1c9b6]">
                <p class="text-xs text-[#41493b]">
                    Affichage de {{ $candidates->firstItem() }} à {{ $candidates->lastItem() }} sur {{ $candidates->total() }} candidats
                </p>
                {{ $candidates->links('livewire.partials.pagination') }}
            </div>
        @endif
    </div>

    {{-- Loading overlay --}}
    <div wire:loading.flex class="fixed inset-0 bg-black/10 z-50 items-center justify-center">
        <div class="bg-white rounded-2xl p-4 shadow-xl flex items-center gap-3">
            <span class="material-symbols-outlined text-[#2c6904] animate-spin">progress_activity</span>
            <span class="text-sm font-medium text-[#1e1b18]">Chargement...</span>
        </div>
    </div>

</div>
