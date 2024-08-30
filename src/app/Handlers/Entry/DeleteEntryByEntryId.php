<?php

namespace Cyvian\Src\app\Handlers\Entry;

use Cyvian\Src\app\Classes\Entry;
use Cyvian\Src\app\Handlers\Field\DeleteFieldsByEntityIdAndEntityType;
use Cyvian\Src\app\Handlers\Section\GetSectionIdsByEntityIdAndEntityType;
use Cyvian\Src\app\Repositories\ActionEntryRoleRepository;
use Cyvian\Src\app\Repositories\EntryRepository;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;

class DeleteEntryByEntryId
{
    private $entryRepository;
    private $actionEntryRoleRepository;
    private $fieldRepository;
    private $fieldAttributeRepository;
    private $fieldValueRepository;

    public function __construct(
        EntryRepository $entryRepository,
        ActionEntryRoleRepository $actionEntryRoleRepository,
        FieldRepository $fieldRepository,
        FieldAttributeRepository $fieldAttributeRepository,
        FieldValueRepository $fieldValueRepository
    )
    {
        $this->entryRepository = $entryRepository;
        $this->actionEntryRoleRepository = $actionEntryRoleRepository;
        $this->fieldRepository = $fieldRepository;
        $this->fieldAttributeRepository = $fieldAttributeRepository;
        $this->fieldValueRepository = $fieldValueRepository;
    }

    public function handle(int $entryId)
    {
        $getSectionIdsByEntityIdAndEntityType = new GetSectionIdsByEntityIdAndEntityType(
            $this->sectionRepository,
        );

        $sectionIds = $getSectionIdsByEntityIdAndEntityType->handle($entryId, Entry::class);

        foreach ($sectionIds as $sectionId) {
            $deleteFieldsByEntityIdAndEntityType = new DeleteFieldsByEntityIdAndEntityType(
                $this->fieldRepository,
                $this->fieldAttributeRepository,
                $this->fieldValueRepository
            );
            $deleteFieldsByEntityIdAndEntityType->handle($sectionId, Entry::class);
        }

        $this->actionEntryRoleRepository->deleteActionEntryRoleByEntryId($entryId);
        $this->entryRepository->deleteEntryById($entryId);
    }
}
