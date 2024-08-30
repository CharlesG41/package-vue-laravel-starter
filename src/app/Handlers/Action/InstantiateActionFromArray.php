<?php

namespace Cyvian\Src\app\Handlers\Action;

use Cyvian\Src\app\Classes\Action;
use Cyvian\Src\app\Classes\Translations\ActionTranslation;
use Cyvian\Src\app\Handlers\Field\InstantiateFieldFromArray;
use Cyvian\Src\app\Utils\Localisation;

class InstantiateActionFromArray
{
    public function handle(array $data): Action
    {
        $instantiateFieldFromArray = new InstantiateFieldFromArray();

        $fields = [];
        $data['fields'] = $data['fields'] ?? [];
        foreach($data['fields'] as $field) {
            $fields[] = $instantiateFieldFromArray->handle($field);
        }

        $label = array_key_exists('label', $data) ? $data['label'] ?? [] : [];
        $message = array_key_exists('message', $data) ? $data['message'] ?? [] : [];
        $actionLabel = array_key_exists('action_label', $data) ? $data['action_label'] ?? [] : [];

        $action = new Action(
            $data['name'],
            $data['position'],
            $data['action_type'],
            $data['url'],
            $data['roles_by_entry'] ?? false,
            new ActionTranslation(
                new Localisation($label),
                new Localisation($message),
                new Localisation($actionLabel)
            ),
            $data['roles_ids'] ?? [1],
            $fields
        );

        if(array_key_exists('id', $data)) {
            $action->setId($data['id']);
        }
        if(array_key_exists('entry_type_id', $data)) {
            $action->setEntryTypeId($data['entry_type_id']);
        }

        return $action;
    }
}
