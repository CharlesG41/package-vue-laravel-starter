<?php

namespace Cyvian\Src\app\Handlers\Action;

use Cyvian\Src\app\Handlers\Field\DeleteFieldsByEntityIdAndEntityType;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;

class DeleteActionsByEntryTypeId
{
    private $actionRepository;
    private $fieldRepository;
    private $fieldAttributeRepository;

    public function __construct(ActionRepository $actionRepository, FieldRepository $fieldRepository, FieldAttributeRepository $fieldAttributeRepository)
    {
        $this->actionRepository = $actionRepository;
        $this->fieldRepository = $fieldRepository;
        $this->fieldAttributeRepository = $fieldAttributeRepository;
    }

    public function handle(int $entryTypeId)
    {
        $deleteFieldsByEntityIdAndEntityType =  new DeleteFieldsByEntityIdAndEntityType($this->fieldRepository, $this->fieldAttributeRepository);
        $deleteFieldsByEntityIdAndEntityType->handle($entryTypeId, 'entry_type');

        $this->actionRepository->deleteActionsByEntryTypeId($entryTypeId);
    }
}
