<?php

namespace Cyvian\Src\app\Handlers\Form;

use Cyvian\Src\app\Classes\Form;

class MergeEntryTypeFormWithEntryForm
{
    public function handle(Form $entryTypeForm, Form $entryForm)
    {
        $topFields = array_merge($entryTypeForm->topFields, $entryForm->topFields);
        $rightFields = array_merge($entryTypeForm->rightFields, $entryForm->rightFields);
        $tabs = array_merge($entryTypeForm->tabs, $entryForm->tabs);

        return new Form(
            $topFields,
            $rightFields,
            $tabs
        );
    }
}
