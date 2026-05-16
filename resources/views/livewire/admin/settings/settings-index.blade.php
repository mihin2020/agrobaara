<div class="max-w-6xl mx-auto space-y-6">

    {{-- En-tête --}}
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-[#2c6904] flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-white text-lg">settings</span>
        </div>
        <div>
            <h1 class="font-sora text-xl font-bold text-[#1e1b18] leading-tight">Paramètres</h1>
            <p class="text-xs text-[#717a69] mt-0.5">Gestion des référentiels utilisés dans les formulaires</p>
        </div>
    </div>

    {{-- Layout deux colonnes --}}
    <div class="flex gap-5 items-start">

        {{-- ─── Sidebar navigation ───────────────────────────────────── --}}
        <div class="w-56 flex-shrink-0 space-y-2">

            @php
                $sections = [
                    ['key' => 'languages',     'icon' => 'translate',   'label' => 'Langues parlées',              'count' => $languages->count(),     'color' => 'blue'],
                    ['key' => 'educations',    'icon' => 'school',      'label' => "Niveaux d'étude",              'count' => $educations->count(),    'color' => 'purple'],
                    ['key' => 'nationalities', 'icon' => 'flag',        'label' => 'Nationalités',                 'count' => $nationalities->count(), 'color' => 'amber'],
                    ['key' => 'skills',        'icon' => 'agriculture', 'label' => 'Compétences agroécologiques',  'count' => $skills->count(),        'color' => 'green'],
                ];
            @endphp

            @foreach($sections as $section)
                <button wire:click="setTab('{{ $section['key'] }}')"
                        class="w-full text-left rounded-2xl border transition-all p-3.5 group
                            {{ $activeTab === $section['key']
                                ? 'bg-white border-[#2c6904]/30 shadow-sm'
                                : 'bg-white/60 border-[#c1c9b6] hover:bg-white hover:border-[#c1c9b6]' }}">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 transition-colors
                            {{ $activeTab === $section['key'] ? 'bg-[#2c6904]' : 'bg-[#f5ece7] group-hover:bg-[#2c6904]/10' }}">
                            <span class="material-symbols-outlined text-sm
                                {{ $activeTab === $section['key'] ? 'text-white' : 'text-[#2c6904]' }}">{{ $section['icon'] }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-[#1e1b18] leading-tight truncate">{{ $section['label'] }}</p>
                            <p class="text-[11px] text-[#717a69] mt-0.5">{{ $section['count'] }} élément{{ $section['count'] > 1 ? 's' : '' }}</p>
                        </div>
                        @if($activeTab === $section['key'])
                            <span class="material-symbols-outlined text-sm text-[#2c6904]">chevron_right</span>
                        @endif
                    </div>
                </button>
            @endforeach

        </div>

        {{-- ─── Panneau principal ─────────────────────────────────────── --}}
        <div class="flex-1 min-w-0">

            {{-- ── Langues ─────────────────────────────────────────────── --}}
            @if($activeTab === 'languages')
                <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm overflow-hidden">

                    {{-- Header --}}
                    <div class="px-6 py-4 border-b border-[#c1c9b6] bg-[#f5f9f2] flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-base text-[#2c6904]">translate</span>
                            <div>
                                <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Langues parlées</h3>
                                <p class="text-xs text-[#717a69]">Langues disponibles dans le formulaire candidat</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 bg-[#2c6904]/10 text-[#2c6904] rounded-full">{{ $languages->count() }}</span>
                    </div>

                    {{-- Formulaire inline --}}
                    <div class="px-6 py-4 border-b border-[#c1c9b6] bg-[#fbfdfb]">
                        @if(session('success_lang'))
                            <div class="mb-3 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 rounded-xl px-3 py-2 text-xs">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                {{ session('success_lang') }}
                            </div>
                        @endif
                        <div class="flex gap-2.5">
                            <div class="flex-1">
                                <input type="text" wire:model="newLanguageName" wire:keydown.enter="saveLanguage"
                                       placeholder="Nom de la langue  — ex : Bambara"
                                       class="w-full px-3.5 py-2.5 bg-[#f5ece7] border {{ $errors->has('newLanguageName') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all" />
                                @error('newLanguageName') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="w-28">
                                <input type="text" wire:model="newLanguageCode" wire:keydown.enter="saveLanguage"
                                       placeholder="Code · ex : bm"
                                       class="w-full px-3.5 py-2.5 bg-[#f5ece7] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all" />
                            </div>
                            <button wire:click="saveLanguage"
                                    class="flex items-center gap-1.5 px-4 py-2.5 bg-[#2c6904] text-white text-sm font-semibold rounded-xl hover:bg-[#3a8406] transition-colors whitespace-nowrap shadow-sm shadow-[#2c6904]/20">
                                <span class="material-symbols-outlined text-base">add</span>
                                Ajouter
                            </button>
                        </div>
                    </div>

                    {{-- Liste --}}
                    <div class="divide-y divide-[#c1c9b6]/30">
                        @forelse($languages as $lang)
                            <div class="flex items-center justify-between px-6 py-3 hover:bg-[#fbf2ed]/40 transition-colors">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-[#f5ece7] flex-shrink-0">
                                        <span class="material-symbols-outlined text-sm text-[#2c6904]">translate</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-[#1e1b18] {{ !$lang->is_active ? 'opacity-50' : '' }}">{{ $lang->name }}</p>
                                        @if($lang->code)
                                            <span class="text-[11px] font-mono text-[#717a69] bg-[#f5ece7] px-1.5 py-0.5 rounded">{{ $lang->code }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    {{-- Toggle switch --}}
                                    <button wire:click="toggleLanguage('{{ $lang->id }}')"
                                            title="{{ $lang->is_active ? 'Désactiver' : 'Activer' }}"
                                            class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none
                                                {{ $lang->is_active ? 'bg-[#2c6904]' : 'bg-[#c1c9b6]' }}">
                                        <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow transition-transform
                                            {{ $lang->is_active ? 'translate-x-4' : 'translate-x-1' }}"></span>
                                    </button>
                                    <button wire:click="deleteLanguage('{{ $lang->id }}')"
                                            wire:confirm="Supprimer « {{ $lang->name }} » ? Action irréversible."
                                            class="p-1.5 text-[#c1c9b6] hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-[#c1c9b6] mb-2 block">translate</span>
                                <p class="text-sm text-[#717a69]">Aucune langue configurée.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            {{-- ── Niveaux d'étude ──────────────────────────────────────── --}}
            @if($activeTab === 'educations')
                <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm overflow-hidden">

                    <div class="px-6 py-4 border-b border-[#c1c9b6] bg-[#f5f9f2] flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-base text-[#2c6904]">school</span>
                            <div>
                                <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Niveaux d'étude</h3>
                                <p class="text-xs text-[#717a69]">Niveaux proposés dans le formulaire candidat</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 bg-[#2c6904]/10 text-[#2c6904] rounded-full">{{ $educations->count() }}</span>
                    </div>

                    <div class="px-6 py-4 border-b border-[#c1c9b6] bg-[#fbfdfb]">
                        @if(session('success_edu'))
                            <div class="mb-3 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 rounded-xl px-3 py-2 text-xs">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                {{ session('success_edu') }}
                            </div>
                        @endif
                        <div class="flex gap-2.5">
                            <div class="flex-1">
                                <input type="text" wire:model="newEducationName" wire:keydown.enter="saveEducation"
                                       placeholder="Libellé  — ex : BTS / DUT"
                                       class="w-full px-3.5 py-2.5 bg-[#f5ece7] border {{ $errors->has('newEducationName') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all" />
                                @error('newEducationName') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="w-36">
                                <input type="text" wire:model="newEducationCode" wire:keydown.enter="saveEducation"
                                       placeholder="Code  — ex : bts"
                                       class="w-full px-3.5 py-2.5 bg-[#f5ece7] border {{ $errors->has('newEducationCode') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all" />
                                @error('newEducationCode') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <button wire:click="saveEducation"
                                    class="flex items-center gap-1.5 px-4 py-2.5 bg-[#2c6904] text-white text-sm font-semibold rounded-xl hover:bg-[#3a8406] transition-colors whitespace-nowrap shadow-sm shadow-[#2c6904]/20">
                                <span class="material-symbols-outlined text-base">add</span>
                                Ajouter
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-[#717a69]">Le code est unique, en minuscules, sans espaces (lettres, chiffres, underscores).</p>
                    </div>

                    <div class="divide-y divide-[#c1c9b6]/30">
                        @forelse($educations as $edu)
                            <div class="flex items-center justify-between px-6 py-3 hover:bg-[#fbf2ed]/40 transition-colors">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-[#f5ece7] flex-shrink-0">
                                        <span class="material-symbols-outlined text-sm text-[#2c6904]">school</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-[#1e1b18] {{ !$edu->is_active ? 'opacity-50' : '' }}">{{ $edu->name }}</p>
                                        <span class="text-[11px] font-mono text-[#717a69] bg-[#f5ece7] px-1.5 py-0.5 rounded">{{ $edu->code }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <button wire:click="toggleEducation('{{ $edu->id }}')"
                                            title="{{ $edu->is_active ? 'Désactiver' : 'Activer' }}"
                                            class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none
                                                {{ $edu->is_active ? 'bg-[#2c6904]' : 'bg-[#c1c9b6]' }}">
                                        <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow transition-transform
                                            {{ $edu->is_active ? 'translate-x-4' : 'translate-x-1' }}"></span>
                                    </button>
                                    <button wire:click="deleteEducation('{{ $edu->id }}')"
                                            wire:confirm="Supprimer « {{ $edu->name }} » ? Action irréversible."
                                            class="p-1.5 text-[#c1c9b6] hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-[#c1c9b6] mb-2 block">school</span>
                                <p class="text-sm text-[#717a69]">Aucun niveau d'étude configuré.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            {{-- ── Nationalités ─────────────────────────────────────────── --}}
            @if($activeTab === 'nationalities')
                <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm overflow-hidden">

                    <div class="px-6 py-4 border-b border-[#c1c9b6] bg-[#f5f9f2] flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-base text-[#2c6904]">flag</span>
                            <div>
                                <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Nationalités</h3>
                                <p class="text-xs text-[#717a69]">Nationalités disponibles dans le formulaire candidat</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 bg-[#2c6904]/10 text-[#2c6904] rounded-full">{{ $nationalities->count() }}</span>
                    </div>

                    <div class="px-6 py-4 border-b border-[#c1c9b6] bg-[#fbfdfb]">
                        @if(session('success_nat'))
                            <div class="mb-3 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 rounded-xl px-3 py-2 text-xs">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                {{ session('success_nat') }}
                            </div>
                        @endif
                        <div class="flex gap-2.5">
                            <div class="flex-1">
                                <input type="text" wire:model="newNationalityName" wire:keydown.enter="saveNationality"
                                       placeholder="Nationalité  — ex : Libérien(ne)"
                                       class="w-full px-3.5 py-2.5 bg-[#f5ece7] border {{ $errors->has('newNationalityName') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all" />
                                @error('newNationalityName') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <button wire:click="saveNationality"
                                    class="flex items-center gap-1.5 px-4 py-2.5 bg-[#2c6904] text-white text-sm font-semibold rounded-xl hover:bg-[#3a8406] transition-colors whitespace-nowrap shadow-sm shadow-[#2c6904]/20">
                                <span class="material-symbols-outlined text-base">add</span>
                                Ajouter
                            </button>
                        </div>
                    </div>

                    {{-- Liste en grille pour les nationalités (souvent longue liste) --}}
                    <div class="p-4">
                        @if($nationalities->isEmpty())
                            <div class="py-10 text-center">
                                <span class="material-symbols-outlined text-3xl text-[#c1c9b6] mb-2 block">flag</span>
                                <p class="text-sm text-[#717a69]">Aucune nationalité configurée.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($nationalities as $nat)
                                    <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl border border-[#c1c9b6]/60 hover:border-[#2c6904]/30 hover:bg-[#fbf2ed]/30 transition-all">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="text-sm {{ $nat->is_active ? 'font-semibold text-[#1e1b18]' : 'text-[#717a69] line-through' }} truncate">{{ $nat->name }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 flex-shrink-0 ml-2">
                                            <button wire:click="toggleNationality('{{ $nat->id }}')"
                                                    title="{{ $nat->is_active ? 'Désactiver' : 'Activer' }}"
                                                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none
                                                        {{ $nat->is_active ? 'bg-[#2c6904]' : 'bg-[#c1c9b6]' }}">
                                                <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow transition-transform
                                                    {{ $nat->is_active ? 'translate-x-4' : 'translate-x-1' }}"></span>
                                            </button>
                                            <button wire:click="deleteNationality('{{ $nat->id }}')"
                                                    wire:confirm="Supprimer « {{ $nat->name }} » ?"
                                                    class="p-1 text-[#c1c9b6] hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                                <span class="material-symbols-outlined text-base">delete</span>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- ── Compétences ──────────────────────────────────────────── --}}
            @if($activeTab === 'skills')
                <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm overflow-hidden">

                    <div class="px-6 py-4 border-b border-[#c1c9b6] bg-[#f5f9f2] flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-base text-[#2c6904]">agriculture</span>
                            <div>
                                <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Compétences agroécologiques</h3>
                                <p class="text-xs text-[#717a69]">Compétences disponibles pour les candidats et offres</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 bg-[#2c6904]/10 text-[#2c6904] rounded-full">{{ $skills->count() }}</span>
                    </div>

                    <div class="px-6 py-4 border-b border-[#c1c9b6] bg-[#fbfdfb]">
                        @if(session('success_skill'))
                            <div class="mb-3 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 rounded-xl px-3 py-2 text-xs">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                {{ session('success_skill') }}
                            </div>
                        @endif
                        <div class="flex gap-2.5">
                            <div class="flex-1">
                                <input type="text" wire:model="newSkillName" wire:keydown.enter="saveSkill"
                                       placeholder="Compétence  — ex : Sylviculture"
                                       class="w-full px-3.5 py-2.5 bg-[#f5ece7] border {{ $errors->has('newSkillName') ? 'border-red-400' : 'border-[#c1c9b6]' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all" />
                                @error('newSkillName') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="w-40">
                                <input type="text" wire:model="newSkillCategory" wire:keydown.enter="saveSkill"
                                       placeholder="Catégorie (optionnel)"
                                       class="w-full px-3.5 py-2.5 bg-[#f5ece7] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] transition-all" />
                            </div>
                            <button wire:click="saveSkill"
                                    class="flex items-center gap-1.5 px-4 py-2.5 bg-[#2c6904] text-white text-sm font-semibold rounded-xl hover:bg-[#3a8406] transition-colors whitespace-nowrap shadow-sm shadow-[#2c6904]/20">
                                <span class="material-symbols-outlined text-base">add</span>
                                Ajouter
                            </button>
                        </div>
                    </div>

                    {{-- Liste groupée par catégorie --}}
                    @php $grouped = $skills->groupBy('category'); @endphp
                    @if($skills->isEmpty())
                        <div class="py-12 text-center">
                            <span class="material-symbols-outlined text-3xl text-[#c1c9b6] mb-2 block">agriculture</span>
                            <p class="text-sm text-[#717a69]">Aucune compétence configurée.</p>
                        </div>
                    @else
                        @foreach($grouped as $category => $group)
                            {{-- Séparateur de catégorie --}}
                            <div class="px-6 py-2 bg-[#f5ece7]/50 flex items-center gap-2 border-b border-[#c1c9b6]/30">
                                <span class="material-symbols-outlined text-xs text-[#875212]">label</span>
                                <p class="text-[11px] font-bold text-[#875212] uppercase tracking-wider">{{ $category ?: 'Sans catégorie' }}</p>
                                <span class="text-[10px] text-[#717a69] ml-auto">{{ $group->count() }}</span>
                            </div>
                            <div class="divide-y divide-[#c1c9b6]/20">
                                @foreach($group as $skill)
                                    <div class="flex items-center justify-between px-6 py-2.5 hover:bg-[#fbf2ed]/40 transition-colors">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="text-sm {{ $skill->is_active ? 'font-semibold text-[#1e1b18]' : 'text-[#717a69] line-through' }} truncate">{{ $skill->name }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <button wire:click="toggleSkill('{{ $skill->id }}')"
                                                    title="{{ $skill->is_active ? 'Désactiver' : 'Activer' }}"
                                                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none
                                                        {{ $skill->is_active ? 'bg-[#2c6904]' : 'bg-[#c1c9b6]' }}">
                                                <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow transition-transform
                                                    {{ $skill->is_active ? 'translate-x-4' : 'translate-x-1' }}"></span>
                                            </button>
                                            <button wire:click="deleteSkill('{{ $skill->id }}')"
                                                    wire:confirm="Supprimer « {{ $skill->name }} » ?"
                                                    class="p-1.5 text-[#c1c9b6] hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                                <span class="material-symbols-outlined text-base">delete</span>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @endif
                </div>
            @endif

        </div>{{-- fin panneau principal --}}
    </div>{{-- fin layout deux colonnes --}}

</div>
