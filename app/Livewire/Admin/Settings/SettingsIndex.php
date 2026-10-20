<?php

namespace App\Livewire\Admin\Settings;

use App\Models\ReferentialActivityType;
use App\Models\ReferentialEducationLevel;
use App\Models\ReferentialEmploymentType;
use App\Models\ReferentialFormationType;
use App\Models\ReferentialLanguage;
use App\Models\ReferentialNationality;
use App\Models\ReferentialSkill;
use App\Models\SiteSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Paramètres - Agro Eco BAARA')]
class SettingsIndex extends Component
{
    public string $activeTab = 'general';

    public bool $showAgroecologyMention = true;

    public bool $twoFactorEmailEnabled = true;

    // ── Langues ──────────────────────────────────────────────────────────
    public string $newLanguageName = '';
    public string $newLanguageCode = '';

    // ── Niveaux d'étude ──────────────────────────────────────────────────
    public string $newEducationName = '';
    public string $newEducationCode = '';

    // ── Nationalités ─────────────────────────────────────────────────────
    public string $newNationalityName = '';

    // ── Compétences ───────────────────────────────────────────────────────
    public string $newSkillName     = '';
    public string $newSkillCategory = '';

    // ── Emploi / Formation / Activités ───────────────────────────────────
    public string $newEmploymentName = '';
    public string $newEmploymentCode = '';
    public string $newFormationName  = '';
    public string $newFormationCode  = '';
    public string $newActivityName   = '';

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->hasPermission('settings.view')
            || auth()->user()?->hasPermission('referentials.manage'),
            403
        );
        $this->showAgroecologyMention = SiteSetting::showAgroecologyMention();
        $this->twoFactorEmailEnabled = SiteSetting::twoFactorEmailEnabled();
    }

    protected function authorizeSettingsUpdate(): void
    {
        abort_unless(auth()->user()?->hasPermission('settings.update'), 403);
    }

    protected function authorizeReferentials(): void
    {
        abort_unless(auth()->user()?->hasPermission('referentials.manage'), 403);
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetValidation();
    }

    public function saveGeneralSettings(): void
    {
        $this->authorizeSettingsUpdate();
        SiteSetting::set('show_agroecology_mention', $this->showAgroecologyMention, 'boolean');
        SiteSetting::set('two_factor_email_enabled', $this->twoFactorEmailEnabled, 'boolean');
        $this->dispatch('notify', type: 'success', message: 'Paramètres généraux enregistrés.');
    }

    // ── Languages ─────────────────────────────────────────────────────────

    public function saveLanguage(): void
    {
        $this->authorizeReferentials();
        $this->validate([
            'newLanguageName' => 'required|string|min:2|max:100|unique:referentials_languages,name',
            'newLanguageCode' => 'nullable|string|max:10|unique:referentials_languages,code',
        ], [
            'newLanguageName.required' => 'Le nom de la langue est obligatoire.',
            'newLanguageName.min'      => 'Le nom doit contenir au moins 2 caractères.',
            'newLanguageName.unique'   => 'Cette langue existe déjà.',
            'newLanguageCode.max'      => 'Le code ne peut pas dépasser 10 caractères.',
            'newLanguageCode.unique'   => 'Ce code de langue est déjà utilisé.',
        ]);

        ReferentialLanguage::create([
            'name'      => trim($this->newLanguageName),
            'code'      => trim($this->newLanguageCode) ?: null,
            'is_active' => true,
        ]);

        $this->reset('newLanguageName', 'newLanguageCode');
        $this->dispatch('notify', type: 'success', message: 'Langue ajoutée avec succès.');
    }

    public function toggleLanguage(string $id): void
    {
        $this->authorizeReferentials();
        $lang = ReferentialLanguage::findOrFail($id);
        $lang->update(['is_active' => !$lang->is_active]);
        $this->dispatch(
            'notify',
            type: 'success',
            message: $lang->is_active ? 'Langue activée.' : 'Langue désactivée.'
        );
    }

    public function deleteLanguage(string $id): void
    {
        $this->authorizeReferentials();
        ReferentialLanguage::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Langue supprimée.');
    }

    // ── Nationalities ────────────────────────────────────────────────────

    public function saveNationality(): void
    {
        $this->authorizeReferentials();
        $this->validateOnly('newNationalityName', [
            'newNationalityName' => 'required|string|min:2|max:100|unique:referentials_nationalities,name',
        ], [
            'newNationalityName.required' => 'Le nom est obligatoire.',
            'newNationalityName.min'      => 'Minimum 2 caractères.',
            'newNationalityName.unique'   => 'Cette nationalité existe déjà.',
        ]);

        ReferentialNationality::create(['name' => trim($this->newNationalityName), 'is_active' => true]);
        $this->reset('newNationalityName');
        $this->dispatch('notify', type: 'success', message: 'Nationalité ajoutée avec succès.');
    }

    public function toggleNationality(string $id): void
    {
        $this->authorizeReferentials();
        $nat = ReferentialNationality::findOrFail($id);
        $nat->update(['is_active' => !$nat->is_active]);
        $this->dispatch(
            'notify',
            type: 'success',
            message: $nat->is_active ? 'Nationalité activée.' : 'Nationalité désactivée.'
        );
    }

    public function deleteNationality(string $id): void
    {
        $this->authorizeReferentials();
        ReferentialNationality::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Nationalité supprimée.');
    }

    // ── Education Levels ─────────────────────────────────────────────────

    public function saveEducation(): void
    {
        $this->authorizeReferentials();
        $this->validate([
            'newEducationName' => 'required|string|min:2|max:100|unique:referentials_education_levels,name',
            'newEducationCode' => 'required|string|min:2|max:50|unique:referentials_education_levels,code|regex:/^[a-z0-9_]+$/',
        ], [
            'newEducationName.required' => 'Le libellé est obligatoire.',
            'newEducationName.unique'   => 'Ce niveau d\'étude existe déjà.',
            'newEducationCode.required' => 'Le code est obligatoire.',
            'newEducationCode.unique'   => 'Ce code est déjà utilisé.',
            'newEducationCode.regex'    => 'Le code ne peut contenir que des lettres minuscules, chiffres et underscores.',
        ]);

        $maxOrder = ReferentialEducationLevel::max('sort_order') ?? 0;

        ReferentialEducationLevel::create([
            'name'       => trim($this->newEducationName),
            'code'       => trim($this->newEducationCode),
            'is_active'  => true,
            'sort_order' => $maxOrder + 1,
        ]);

        $this->reset('newEducationName', 'newEducationCode');
        $this->dispatch('notify', type: 'success', message: 'Niveau d\'étude ajouté avec succès.');
    }

    public function toggleEducation(string $id): void
    {
        $this->authorizeReferentials();
        $level = ReferentialEducationLevel::findOrFail($id);
        $level->update(['is_active' => !$level->is_active]);
        $this->dispatch(
            'notify',
            type: 'success',
            message: $level->is_active ? 'Niveau d\'étude activé.' : 'Niveau d\'étude désactivé.'
        );
    }

    public function deleteEducation(string $id): void
    {
        $this->authorizeReferentials();
        ReferentialEducationLevel::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Niveau d\'étude supprimé.');
    }

    // ── Skills ────────────────────────────────────────────────────────────

    public function saveSkill(): void
    {
        $this->authorizeReferentials();
        $this->validate([
            'newSkillName' => 'required|string|min:2|max:150|unique:referentials_skills,name',
        ], [
            'newSkillName.required' => 'Le nom de la compétence est obligatoire.',
            'newSkillName.min'      => 'Le nom doit contenir au moins 2 caractères.',
            'newSkillName.unique'   => 'Cette compétence existe déjà.',
        ]);

        ReferentialSkill::create([
            'name'      => trim($this->newSkillName),
            'category'  => trim($this->newSkillCategory) ?: null,
            'is_active' => true,
        ]);

        $this->reset('newSkillName', 'newSkillCategory');
        $this->dispatch('notify', type: 'success', message: 'Compétence ajoutée avec succès.');
    }

    public function toggleSkill(string $id): void
    {
        $this->authorizeReferentials();
        $skill = ReferentialSkill::findOrFail($id);
        $skill->update(['is_active' => !$skill->is_active]);
        $this->dispatch(
            'notify',
            type: 'success',
            message: $skill->is_active ? 'Compétence activée.' : 'Compétence désactivée.'
        );
    }

    public function deleteSkill(string $id): void
    {
        $this->authorizeReferentials();
        ReferentialSkill::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Compétence supprimée.');
    }

    // ── Employment types ─────────────────────────────────────────────────

    public function saveEmploymentType(): void
    {
        $this->authorizeReferentials();
        $this->validate([
            'newEmploymentName' => 'required|string|min:2|max:150',
            'newEmploymentCode' => 'required|string|min:2|max:50|unique:referentials_employment_types,code|regex:/^[a-z0-9_]+$/',
        ], [
            'newEmploymentName.required' => 'Le libellé est obligatoire.',
            'newEmploymentCode.required' => 'Le code est obligatoire.',
            'newEmploymentCode.unique'   => 'Ce code est déjà utilisé.',
            'newEmploymentCode.regex'    => 'Code : lettres minuscules, chiffres, underscores.',
        ]);

        $max = ReferentialEmploymentType::max('sort_order') ?? 0;
        ReferentialEmploymentType::create([
            'name'       => trim($this->newEmploymentName),
            'code'       => trim($this->newEmploymentCode),
            'is_active'  => true,
            'sort_order' => $max + 1,
        ]);
        $this->reset('newEmploymentName', 'newEmploymentCode');
        $this->dispatch('notify', type: 'success', message: 'Type d\'emploi ajouté.');
    }

    public function toggleEmploymentType(string $id): void
    {
        $this->authorizeReferentials();
        $row = ReferentialEmploymentType::findOrFail($id);
        $row->update(['is_active' => !$row->is_active]);
        $this->dispatch('notify', type: 'success', message: $row->is_active ? 'Type d\'emploi activé.' : 'Type d\'emploi désactivé.');
    }

    public function deleteEmploymentType(string $id): void
    {
        $this->authorizeReferentials();
        ReferentialEmploymentType::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Type d\'emploi supprimé.');
    }

    // ── Formation types ──────────────────────────────────────────────────

    public function saveFormationType(): void
    {
        $this->authorizeReferentials();
        $this->validate([
            'newFormationName' => 'required|string|min:2|max:150',
            'newFormationCode' => 'required|string|min:2|max:50|unique:referentials_formation_types,code|regex:/^[a-z0-9_]+$/',
        ], [
            'newFormationName.required' => 'Le libellé est obligatoire.',
            'newFormationCode.required' => 'Le code est obligatoire.',
            'newFormationCode.unique'   => 'Ce code est déjà utilisé.',
            'newFormationCode.regex'    => 'Code : lettres minuscules, chiffres, underscores.',
        ]);

        $max = ReferentialFormationType::max('sort_order') ?? 0;
        ReferentialFormationType::create([
            'name'       => trim($this->newFormationName),
            'code'       => trim($this->newFormationCode),
            'is_active'  => true,
            'sort_order' => $max + 1,
        ]);
        $this->reset('newFormationName', 'newFormationCode');
        $this->dispatch('notify', type: 'success', message: 'Type de formation ajouté.');
    }

    public function toggleFormationType(string $id): void
    {
        $this->authorizeReferentials();
        $row = ReferentialFormationType::findOrFail($id);
        $row->update(['is_active' => !$row->is_active]);
        $this->dispatch('notify', type: 'success', message: $row->is_active ? 'Type de formation activé.' : 'Type de formation désactivé.');
    }

    public function deleteFormationType(string $id): void
    {
        $this->authorizeReferentials();
        ReferentialFormationType::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Type de formation supprimé.');
    }

    // ── Activity types ───────────────────────────────────────────────────

    public function saveActivityType(): void
    {
        $this->authorizeReferentials();
        $this->validate([
            'newActivityName' => 'required|string|min:2|max:150|unique:referentials_activity_types,name',
        ], [
            'newActivityName.required' => 'Le nom est obligatoire.',
            'newActivityName.unique'   => 'Ce type d\'activité existe déjà.',
        ]);

        $max = ReferentialActivityType::max('sort_order') ?? 0;
        ReferentialActivityType::create([
            'name'       => trim($this->newActivityName),
            'is_active'  => true,
            'sort_order' => $max + 1,
        ]);
        $this->reset('newActivityName');
        $this->dispatch('notify', type: 'success', message: 'Type d\'activité ajouté.');
    }

    public function toggleActivityType(string $id): void
    {
        $this->authorizeReferentials();
        $row = ReferentialActivityType::findOrFail($id);
        $row->update(['is_active' => !$row->is_active]);
        $this->dispatch('notify', type: 'success', message: $row->is_active ? 'Type d\'activité activé.' : 'Type d\'activité désactivé.');
    }

    public function deleteActivityType(string $id): void
    {
        $this->authorizeReferentials();
        ReferentialActivityType::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Type d\'activité supprimé.');
    }

    public function render()
    {
        return view('livewire.admin.settings.settings-index', [
            'languages'       => ReferentialLanguage::orderBy('name')->get(),
            'nationalities'   => ReferentialNationality::orderBy('name')->get(),
            'educations'      => ReferentialEducationLevel::orderBy('sort_order')->orderBy('name')->get(),
            'skills'          => ReferentialSkill::orderBy('category')->orderBy('name')->get(),
            'employmentTypes' => ReferentialEmploymentType::orderBy('sort_order')->orderBy('name')->get(),
            'formationTypes'  => ReferentialFormationType::orderBy('sort_order')->orderBy('name')->get(),
            'activityTypes'   => ReferentialActivityType::orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }
}
