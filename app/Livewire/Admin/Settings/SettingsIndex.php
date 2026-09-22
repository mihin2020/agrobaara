<?php

namespace App\Livewire\Admin\Settings;

use App\Models\ReferentialEducationLevel;
use App\Models\ReferentialLanguage;
use App\Models\ReferentialNationality;
use App\Models\ReferentialSkill;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Paramètres - Agro Eco BAARA')]
class SettingsIndex extends Component
{
    public string $activeTab = 'languages';

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

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetValidation();
    }

    // ── Languages ─────────────────────────────────────────────────────────

    public function saveLanguage(): void
    {
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
        ReferentialLanguage::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Langue supprimée.');
    }

    // ── Nationalities ────────────────────────────────────────────────────

    public function saveNationality(): void
    {
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
        ReferentialNationality::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Nationalité supprimée.');
    }

    // ── Education Levels ─────────────────────────────────────────────────

    public function saveEducation(): void
    {
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
        ReferentialEducationLevel::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Niveau d\'étude supprimé.');
    }

    // ── Skills ────────────────────────────────────────────────────────────

    public function saveSkill(): void
    {
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
        ReferentialSkill::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Compétence supprimée.');
    }

    public function render()
    {
        return view('livewire.admin.settings.settings-index', [
            'languages'     => ReferentialLanguage::orderBy('name')->get(),
            'nationalities' => ReferentialNationality::orderBy('name')->get(),
            'educations'    => ReferentialEducationLevel::orderBy('sort_order')->orderBy('name')->get(),
            'skills'        => ReferentialSkill::orderBy('category')->orderBy('name')->get(),
        ]);
    }
}
