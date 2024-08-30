<?php

namespace Cyvian\Src\app\Classes\Fields\Classes;

class FieldPermissions implements \JsonSerializable
{
    public $hiddenOnCreate;
    public $hiddenOnEdit;
    public $disabledOnEdit;
    public $rolesOnCreate;
    public $rolesOnEditOrDisable;
    public $rolesOnEditOrHide;

    public function __construct(
        bool $hiddenOnCreate = false,
        bool $hiddenOnEdit = false,
        bool $disabledOnEdit =  false,
        array $rolesOnCreate = [],
        array $rolesOnEditOrDisable = [],
        array $rolesOnEditOrHide = []
    )
    {
        $this->hiddenOnCreate = $hiddenOnCreate;
        $this->hiddenOnEdit = $hiddenOnEdit;
        $this->disabledOnEdit = $disabledOnEdit;
        $this->rolesOnCreate = $rolesOnCreate;
        $this->rolesOnEditOrDisable = $rolesOnEditOrDisable;
        $this->rolesOnEditOrHide = $rolesOnEditOrHide;
    }

    public function jsonSerialize()
    {
        return [
            'hidden_on_create' => $this->hiddenOnCreate,
            'hidden_on_edit' => $this->hiddenOnEdit,
            'disabled_on_edit' => $this->disabledOnEdit,
            'roles_on_create' => $this->rolesOnCreate,
            'roles_on_edit_or_disable' => $this->rolesOnEditOrDisable,
            'roles_on_edit_or_hide' => $this->rolesOnEditOrHide
        ];
    }
}
