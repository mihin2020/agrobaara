<div class="space-y-6">

    {{-- Flash --}}
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
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-sora text-xl font-bold text-[#1e1b18]">Rôles & Permissions</h2>
            <p class="text-[#41493b] mt-1 text-sm">Gérez les rôles et leurs permissions sur tous les modules.</p>
        </div>
        <button wire:click="openCreateModal" type="button"
                class="flex items-center gap-2 px-4 py-2.5 bg-[#2c6904] text-white text-sm font-semibold rounded-xl hover:bg-[#448322] transition-colors">
            <span class="material-symbols-outlined text-base">add</span>
            Nouveau rôle
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Liste des rôles ── --}}
        <div class="space-y-3">
            <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Rôles disponibles</h3>
            @foreach($roles as $role)
                <div @class([
                    'bg-white rounded-2xl border transition-all overflow-hidden',
                    'border-[#2c6904] shadow-md' => $editingRoleId === $role->id,
                    'border-[#c1c9b6]' => $editingRoleId !== $role->id,
                ])>
                    <div class="flex items-start gap-2 p-4">
                        <button type="button"
                                @if($role->slug !== 'super_admin') wire:click="editRole('{{ $role->id }}')" @endif
                                class="flex-1 text-left {{ $role->slug === 'super_admin' ? 'cursor-not-allowed opacity-60' : 'cursor-pointer hover:opacity-80' }}">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-bold text-sm text-[#1e1b18]">{{ $role->name }}</p>
                                @if($role->is_system)
                                    <span class="text-[10px] px-1.5 py-0.5 bg-[#f5ece7] text-[#717a69] rounded font-semibold">Système</span>
                                @endif
                                @if($role->slug === 'super_admin')
                                    <span class="text-[10px] px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded font-semibold">Protégé</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3 mt-1">
                                <p class="text-[11px] text-[#717a69]">{{ $role->users_count }} utilisateur(s)</p>
                                <span class="text-[11px] font-bold text-[#2c6904]">{{ $role->permissions->count() }} permissions</span>
                            </div>
                        </button>

                        @if(!$role->is_system)
                            <button type="button" wire:click="confirmDelete('{{ $role->id }}')"
                                    class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors flex-shrink-0">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        @endif
                    </div>
                    @if($editingRoleId === $role->id)
                        <div class="px-4 pb-3">
                            <span class="text-[11px] text-[#2c6904] font-semibold flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">edit</span>
                                En cours de modification →
                            </span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- ── Éditeur de permissions ── --}}
        <div class="lg:col-span-2">
            @if($editingRoleId)
                @php $editingRole = $roles->firstWhere('id', $editingRoleId); @endphp
                <div class="bg-white rounded-2xl border border-[#2c6904] shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-[#c1c9b6] bg-[#aef585]/10 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base text-[#2c6904]">admin_panel_settings</span>
                            <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Permissions : {{ $editingRole->name }}</h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="selectAll"
                                    class="text-xs font-semibold text-[#2c6904] hover:underline px-2 py-1 rounded hover:bg-[#aef585]/20 transition-colors">
                                Tout cocher
                            </button>
                            <span class="text-[#c1c9b6]">|</span>
                            <button type="button" wire:click="deselectAll"
                                    class="text-xs font-semibold text-[#717a69] hover:underline px-2 py-1 rounded hover:bg-[#f5ece7] transition-colors">
                                Tout décocher
                            </button>
                            <button type="button" wire:click="cancelEdit" class="ml-2 p-1.5 text-[#717a69] hover:bg-[#f5ece7] rounded-lg">
                                <span class="material-symbols-outlined text-lg">close</span>
                            </button>
                        </div>
                    </div>

                    <div class="divide-y divide-[#f0e8e2] max-h-[62vh] overflow-y-auto">
                        @foreach($allPermissions as $group => $permissions)
                            @php
                                $groupIds     = $permissions->pluck('id')->toArray();
                                $groupChecked = count(array_intersect($groupIds, $checkedPermissions));
                                $groupTotal   = count($groupIds);
                            @endphp
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <p class="text-xs font-bold text-[#717a69] uppercase tracking-widest">{{ $group }}</p>
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full
                                            {{ $groupChecked === $groupTotal ? 'bg-green-100 text-green-700' : ($groupChecked > 0 ? 'bg-amber-100 text-amber-700' : 'bg-[#f5ece7] text-[#717a69]') }}">
                                            {{ $groupChecked }}/{{ $groupTotal }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" wire:click="selectGroup('{{ $group }}')"
                                                class="text-[11px] text-[#2c6904] font-semibold hover:underline">Tout</button>
                                        <span class="text-[#c1c9b6] text-xs">|</span>
                                        <button type="button" wire:click="deselectGroup('{{ $group }}')"
                                                class="text-[11px] text-[#717a69] font-semibold hover:underline">Aucun</button>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-1.5">
                                    @foreach($permissions as $permission)
                                        <label class="flex items-center gap-2.5 px-3 py-2 rounded-xl border cursor-pointer transition-all
                                            {{ in_array($permission->id, $checkedPermissions) ? 'border-[#2c6904] bg-[#aef585]/10' : 'border-[#c1c9b6] hover:border-[#2c6904]/30 hover:bg-[#fbf2ed]' }}">
                                            <input type="checkbox"
                                                   wire:model.live="checkedPermissions"
                                                   value="{{ $permission->id }}"
                                                   class="w-4 h-4 rounded accent-[#2c6904]" />
                                            <span class="text-xs font-semibold text-[#1e1b18] leading-tight">{{ $permission->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-5 py-4 border-t border-[#c1c9b6] bg-[#fbf2ed] flex justify-between items-center">
                        <p class="text-xs text-[#717a69]">
                            <span class="font-bold text-[#1e1b18]">{{ count($checkedPermissions) }}</span> / {{ $allPermissions->flatten()->count() }} permissions
                        </p>
                        <div class="flex gap-3">
                            <button type="button" wire:click="cancelEdit"
                                    class="px-4 py-2 border border-[#c1c9b6] text-[#41493b] font-semibold rounded-xl hover:bg-white transition-colors text-sm">
                                Annuler
                            </button>
                            <button type="button" wire:click="saveRolePermissions"
                                    wire:loading.attr="disabled" wire:target="saveRolePermissions"
                                    class="flex items-center gap-2 px-5 py-2 bg-[#2c6904] text-white font-bold rounded-xl hover:bg-[#448322] transition-colors text-sm">
                                <span wire:loading.remove wire:target="saveRolePermissions" class="material-symbols-outlined text-base">save</span>
                                <span wire:loading wire:target="saveRolePermissions" class="material-symbols-outlined animate-spin text-base">progress_activity</span>
                                Enregistrer
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-dashed border-[#c1c9b6] p-12 text-center">
                    <span class="material-symbols-outlined text-5xl text-[#c1c9b6] block mb-3">admin_panel_settings</span>
                    <p class="text-sm font-semibold text-[#717a69]">Sélectionnez un rôle pour modifier ses permissions.</p>
                    <p class="text-xs text-[#717a69] mt-1">Le rôle super_admin a toutes les permissions par défaut.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Modal création de rôle ── --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
             style="background:rgba(0,0,0,.5)">
            <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-2xl w-full max-w-md">
                <div class="px-6 py-5 border-b border-[#c1c9b6] flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-[#2c6904]">add_circle</span>
                        <h3 class="font-sora font-bold text-sm text-[#1e1b18]">Créer un nouveau rôle</h3>
                    </div>
                    <button type="button" wire:click="$set('showCreateModal', false)"
                            class="p-1.5 text-[#717a69] hover:bg-[#f5ece7] rounded-lg">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Nom du rôle *</label>
                        <input wire:model="newRoleName" type="text"
                               placeholder="Ex: Gestionnaire RH"
                               autofocus
                               class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904] @error('newRoleName') border-red-400 @enderror" />
                        @error('newRoleName')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#41493b] mb-1.5">Description <span class="text-[#717a69] font-normal">(optionnel)</span></label>
                        <input wire:model="newRoleDesc" type="text"
                               placeholder="Ex: Accès lecture seule aux candidats"
                               class="w-full px-3.5 py-2.5 bg-[#fbf2ed] border border-[#c1c9b6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2c6904]/20 focus:border-[#2c6904]" />
                    </div>
                    <p class="text-xs text-[#717a69]">Après création, vous pourrez immédiatement assigner les permissions à ce rôle.</p>
                </div>
                <div class="px-6 py-4 border-t border-[#c1c9b6] bg-[#fbf2ed] flex justify-end gap-3">
                    <button type="button" wire:click="$set('showCreateModal', false)"
                            class="px-4 py-2 border border-[#c1c9b6] text-[#41493b] font-semibold rounded-xl hover:bg-white transition-colors text-sm">
                        Annuler
                    </button>
                    <button type="button" wire:click="createRole"
                            wire:loading.attr="disabled" wire:target="createRole"
                            class="flex items-center gap-2 px-5 py-2 bg-[#2c6904] text-white font-bold rounded-xl hover:bg-[#448322] transition-colors text-sm">
                        <span wire:loading.remove wire:target="createRole" class="material-symbols-outlined text-base">add</span>
                        <span wire:loading wire:target="createRole" class="material-symbols-outlined animate-spin text-base">progress_activity</span>
                        Créer le rôle
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Modal suppression ── --}}
    @if($confirmingDeleteId)
        @php $roleToDelete = $roles->firstWhere('id', $confirmingDeleteId); @endphp
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
             style="background:rgba(0,0,0,.5)">
            <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-2xl w-full max-w-sm">
                <div class="p-6 text-center">
                    <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-2xl text-red-600">delete_forever</span>
                    </div>
                    <h3 class="font-sora font-bold text-base text-[#1e1b18] mb-2">Supprimer le rôle ?</h3>
                    <p class="text-sm text-[#41493b]">
                        Le rôle <strong>{{ $roleToDelete?->name }}</strong> sera définitivement supprimé.
                    </p>
                </div>
                <div class="px-6 pb-6 flex gap-3">
                    <button type="button" wire:click="cancelDelete"
                            class="flex-1 py-2.5 border border-[#c1c9b6] text-[#41493b] font-semibold rounded-xl hover:bg-[#f5ece7] transition-colors text-sm">
                        Annuler
                    </button>
                    <button type="button" wire:click="deleteRole"
                            wire:loading.attr="disabled"
                            class="flex-1 py-2.5 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition-colors text-sm">
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>