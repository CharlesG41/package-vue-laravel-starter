<?php

namespace Cyvian\Src\app\Handlers\FieldGroup;

use Cyvian\Src\app\Classes\FieldGroup;
use Cyvian\Src\app\Handlers\Field\GetFieldsByEntityIdAndEntityType;
use Cyvian\Src\app\Handlers\FieldGroup\FieldGroupTranslation\GetFieldGroupTranslationsByFieldGroupId;
use Cyvian\Src\app\Repositories\FieldGroupRepository;
use Cyvian\Src\app\Repositories\FieldGroupTranslationRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;

class GetFieldGroupsByEntryTypeId
{
    private $fieldGroupRepository;
    private $fieldGroupTranslationRepository;
    private $localeRepository;
    private $fieldRepository;

    public function __construct(
        FieldGroupRepository $fieldGroupRepository,
        FieldGroupTranslationRepository $fieldGroupTranslationRepository,
        LocaleRepository $localeRepository,
        FieldRepository $fieldRepository
    )
    {
        $this->fieldGroupRepository = $fieldGroupRepository;
        $this->fieldGroupTranslationRepository = $fieldGroupTranslationRepository;
        $this->localeRepository = $localeRepository;
        $this->fieldRepository = $fieldRepository;
    }

    public function handle(int $entryTypeId): array
    {
        $getFieldGroupTranslationsByFieldGroupId = new GetFieldGroupTranslationsByFieldGroupId($this->fieldGroupTranslationRepository, $this->localeRepository);
        $getFieldsByEntityIdAndEntityTypeId = new GetFieldsByEntityIdAndEntityType($this->fieldRepository);

        $fieldGroupIds = $this->fieldGroupRepository->getFieldGroupIdsByEntryTypeId($entryTypeId);

        $fieldGroups = [];
        foreach($fieldGroupIds as $fieldGroupId){
            $fieldGroupTranslation = $getFieldGroupTranslationsByFieldGroupId->handle($fieldGroupId);
            $fields = $getFieldsByEntityIdAndEntityTypeId->handle($fieldGroupId, FieldGroup::class);

            $fieldGroup = new FieldGroup(
                $fieldGroupTranslation,
                $fields
            );
            $fieldGroup->setId($fieldGroupId);

            $fieldGroups[] = $fieldGroup;
        }

        return $fieldGroups;
    }
}
