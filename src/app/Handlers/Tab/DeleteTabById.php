<?php

namespace Cyvian\Src\app\Handlers\Tab;

use Cyvian\Src\app\Classes\Tab;
use Cyvian\Src\app\Handlers\Field\DeleteFieldById;
use Cyvian\Src\app\Handlers\Field\GetFieldIdsByEntityIdAndEntityType;
use Cyvian\Src\app\Handlers\Tab\TabTranslation\DeleteTabTranslationByTabId;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\app\Repositories\TabRepository;
use Cyvian\Src\app\Repositories\TabTranslationRepository;

class DeleteTabById
{
    private $tabRepository;
    private $tabTranslationRepository;
    private $fieldRepository;
    private $fieldAttributeRepository;
    private $fieldValueRepository;

    public function __construct(
        TabRepository $tabRepository,
        TabTranslationRepository $tabTranslationRepository,
        FieldRepository $fieldRepository,
        FieldAttributeRepository $fieldAttributeRepository,
        FieldValueRepository $fieldValueRepository
    )
    {
        $this->tabRepository = $tabRepository;
        $this->tabTranslationRepository = $tabTranslationRepository;
        $this->fieldRepository = $fieldRepository;
        $this->fieldAttributeRepository = $fieldAttributeRepository;
        $this->fieldValueRepository = $fieldValueRepository;
    }

    public function handle(int $tabId)
    {
        $getFieldIdsByEntityIdAndEntityType = new GetFieldIdsByEntityIdAndEntityType($this->fieldRepository);
        $deleteFieldById = new DeleteFieldById($this->fieldRepository, $this->fieldAttributeRepository, $this->fieldValueRepository);
        $deleteTabTranslationByTabId = new DeleteTabTranslationByTabId($this->tabTranslationRepository);

        $deleteTabTranslationByTabId->handle($tabId);
        $fieldIds = $getFieldIdsByEntityIdAndEntityType->handle($tabId, Tab::class);

        foreach ($fieldIds as $fieldId) {
            $deleteFieldById->handle($fieldId);
        }

        $this->tabRepository->deleteTabById($tabId);
    }
}
