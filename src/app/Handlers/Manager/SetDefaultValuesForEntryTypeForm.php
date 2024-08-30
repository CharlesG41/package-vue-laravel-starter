<?php

namespace Cyvian\Src\app\Handlers\Manager;

use Cyvian\Src\app\Classes\Fields\Classes\FieldPermissions;
use Cyvian\Src\app\Classes\Fields\Slug;
use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Classes\Section;
use Cyvian\Src\app\Classes\Translations\SectionTranslation;
use Cyvian\Src\App\Utils\LocaleHelper;
use Cyvian\Src\app\Utils\Localisation;

class SetDefaultValuesForEntryTypeForm
{
    public function handle(Form $form): Form
    {
        $entryTypeName = 'entry_type';
        $superAdminRoleId = 1;

        $sectionsFields = [
            new Section(
                new SectionTranslation(
                    Localisation::mapEmpty()
                ),
                null,
                Section::POSITION_LEFT,
                [
                    new Slug(
                        'name',
                        Localisation::mapTranslation('cyvian.fields.text'),
                        Localisation::mapEmpty(),
                        true,
                        6,
                        true,
                        1,
                        null,
                        [],
                        new FieldPermissions(),
                        false,
                    ),
                    new Slug(
                        'slug',
                        Localisation::mapTranslation('cyvian.fields.slug'),
                        Localisation::mapEmpty(),
                        false,
                        6,
                        true,
                        1,
                        null,
                        [],
                        new FieldPermissions(),
                        false,
                    )
                ]
            )
        ];

        $topActionsField = [
            [
                'name' => 'mass_delete',
                'label' => Localisation::mapTranslation('cyvian.actions.labels.mass_delete')->toArray(),
                'action_type' => 'execute',
                'url' => '/{entry_type}/actions/{name}',
                'confirm' => true,
                'message' => Localisation::mapTranslation('cyvian.actions.messages.mass_delete')->toArray(),
                'action_label' => Localisation::mapTranslation('cyvian.actions.action_labels.mass_delete')->toArray(),
                'role_ids' => [$superAdminRoleId],
                'fields' => [],
                'tabs' => []
            ],
            [
                'name' => 'create',
                'url' => '/{entry_type}/create',
                'label' => Localisation::mapTranslation('cyvian.actions.labels.create')->toArray(),
                'action_type' => 'view',
                'role_ids' => [$superAdminRoleId],
            ],
        ];

        $rowActionsField = [
            [
                'name' => 'edit',
                'url' => '/{entry_type}/edit/{id}',
                'label' => Localisation::mapTranslation('cyvian.actions.labels.edit')->toArray(),
                'action_type' => 'view',
                'icon' => 'edit',
                'roles_by_entry' => false,
                'role_ids' => [$superAdminRoleId],
            ],
            [
                'name' => 'duplicate',
                'label' => Localisation::mapTranslation('cyvian.actions.labels.duplicate')->toArray(),
                'action_type' => 'execute',
                'url' => '/{entry_type}/actions/{name}',
                'icon' => 'duplicate',
                'action_label' => Localisation::mapTranslation('cyvian.actions.action_labels.duplicate')->toArray(),
                'roles_by_entry' => false,
                'role_ids' => [$superAdminRoleId],
                'fields' => [],
                'tabs' => []
            ],
            [
                'name' => 'delete',
                'label' => Localisation::mapTranslation('cyvian.actions.labels.delete')->toArray(),
                'action_type' => 'execute',
                'url' => '/{entry_type}/actions/{name}',
                'icon' => 'delete',
                'confirm' => true,
                'message' => Localisation::mapTranslation('cyvian.actions.messages.delete')->toArray(),
                'action_label' => Localisation::mapTranslation('cyvian.actions.action_labels.delete')->toArray(),
                'roles_by_entry' => false,
                'role_ids' => [$superAdminRoleId],
                'fields' => [],
                'tabs' => []
            ],
        ];

        // fields
        $form->sections[1]->fields[0]->default = $sectionsFields;
        // list
        $form->sections[2]->fields[0]->fields[0]->default = [$superAdminRoleId];
        // modify fields
        $form->sections[2]->fields[0]->fields[1]->default = [$superAdminRoleId];
        // top actions
        $form->sections[2]->fields[1]->default = $topActionsField;
        // row actions
        $form->sections[2]->fields[2]->default = $rowActionsField;
        // type
        $form->sections[0]->fields[3]->default = array_keys($form->sections[0]->fields[3]->options)[0];
        // menu section
        $form->sections[0]->fields[4]->default = array_keys($form->sections[0]->fields[4]->options)[0];

//        dd(json_decode(json_encode($form), true));

        return $form;
    }
}
