<?php

namespace Cyvian\Src\app\Classes;

use Cyvian\Src\app\Classes\Fields\Classes\FieldInterface;

class Entry implements \JsonSerializable
{

    const STATUS_PUBLISHED = 'published';
    const STATUS_DRAFT = 'draft';
    const STATUS_SCHEDULED_PUBLICATION = 'scheduled_publication';
    const STATUS_ARCHIVED = 'archived';

    public $id;
    public $order;
    public $entryTypeId;
    public $createdBy;
    public $updatedBy;
    public $actions;
    public $form;

    public function __construct(
        int $order,
        int $entryTypeId,
        int $createdBy,
        int $updatedBy,
        array $actions,
        ?Form $form
    )
    {
        $this->order = $order;
        $this->entryTypeId = $entryTypeId;
        $this->createdBy = $createdBy;
        $this->updatedBy = $updatedBy;
        $this->actions = $actions;
        $this->form = $form;

        foreach($actions as $action) {
            if(!$action instanceof Action) {
                throw new \Exception('Action must be an instance of Action');
            }
        }
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'order' => $this->order,
            'entry_type_id' => $this->entryTypeId,
            'created_by' => $this->createdBy,
            'updated_by' => $this->updatedBy,
            'actions' => $this->actions,
            'form' => $this->form,
        ];
    }
}
