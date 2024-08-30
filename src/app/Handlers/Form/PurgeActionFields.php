<?php

namespace Cyvian\Src\app\Handlers\Form;

use Cyvian\Src\app\Classes\Form;

class PurgeActionFields
{
    public function handle(Form $form): Form
    {
        $form->topFields = $this->removeActionFieldsFromFields($form->topFields);
        $form->rightFields = $this->removeActionFieldsFromFields($form->rightFields);
        foreach($form->tabs as $tab) {
            $tab->fields = $this->removeActionFieldsFromFields($tab->fields);
        }

        return $form;
    }

    private function removeActionFieldsFromFields(array $fields): array
    {
        $newFields = [];
        foreach($fields as $field) {
            if(strpos($field->key, '__action_') === false) {
                $newFields[] = $field;
            }
        }
        return $newFields;
    }
}
