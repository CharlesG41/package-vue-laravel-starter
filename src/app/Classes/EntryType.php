<?php

namespace Cyvian\Src\app\Classes;

use Cyvian\Src\app\Classes\Fields\Classes\FieldInterface;
use Cyvian\Src\app\Classes\Translations\EntryTypeTranslation;
use Cyvian\Src\App\Utils\Localisation;

class EntryType
{
    const TYPE_MODEL = 'model';
    const TYPE_SETTING = 'setting';

    public $name;
    public $type;
    public $menuSectionId;
    public $translation;
    public $form;
    public $actions;
    public $id;

    public function __construct(
        string $name,
        string $type,
        int $menuSectionId,
        EntryTypeTranslation $translations,
        Form $form,
        array $actions
    )
    {
        $this->name = $name;
        $this->type = $type;
        $this->menuSectionId = $menuSectionId;
        $this->translation = $translations;
        $this->form = $form;
        $this->actions = $actions;

        if($type !== EntryType::TYPE_SETTING && $type !== EntryType::TYPE_MODEL) {
            throw new \Exception('Invalid entry type');
        }

        foreach($actions as $action) {
            if(!$action instanceof Action) {
                throw new \Exception('Action must be an instance of Action');
            }
        }
    }

    public function setId(int $id)
    {
        $this->id = $id;
        $this->translation->setTranslationId($id);
    }

//    public function jsonSerialize(): array
//    {
//        return [
//            'name' => $this->name,
//            'type' => $this->type,
//            'menu_section_id' => $this->menuSectionId,
//            'translation' => $this->translation,
//            'form' => $this->form,
//            'actions' => $this->actions,
//            'id' => $this->id
//        ];
//    }
}
