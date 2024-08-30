<?php

namespace Cyvian\Src\app\Classes;

use Cyvian\Src\app\Classes\Translations\MenuSectionTranslation;

class MenuSection implements \JsonSerializable
{
    public $id;
    public $translation;
    public $menuItems;

    public function __construct(
        MenuSectionTranslation $menuSectionTranslation,
        array $menuItems
    )
    {
        $this->translation = $menuSectionTranslation;
        foreach ($menuItems as $menuItem) {
            if (!($menuItem instanceof MenuItem)) {
                throw new \InvalidArgumentException('Menu items must be of type MenuItem');
            }
        }
        $this->menuItems = $menuItems;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->translation->names->getCurrent(),
            'items' => $this->menuItems
        ];
    }
}
