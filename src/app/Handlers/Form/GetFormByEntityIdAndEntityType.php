<?php

namespace Cyvian\Src\app\Handlers\Form;

use Cyvian\Src\app\Classes\Entry;
use Cyvian\Src\app\Classes\Fields\Classes\BaseField;
use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Handlers\Section\GetSectionsByEntityIdAndEntityType;
use Cyvian\Src\app\Handlers\Tab\GetTabsByEntityIdAndEntityType;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;
use Cyvian\Src\app\Repositories\TabRepository;
use Cyvian\Src\app\Repositories\TabTranslationRepository;

class GetFormByEntityIdAndEntityType
{
    private $sectionRepository;
    private $sectionTranslationRepository;
    private $fieldRepository;
    private $localeRepository;

    public function __construct(
        SectionRepository $sectionRepository,
        SectionTranslationRepository $sectionTranslationRepository,
        FieldRepository $fieldRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->sectionRepository = $sectionRepository;
        $this->sectionTranslationRepository = $sectionTranslationRepository;
        $this->fieldRepository = $fieldRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(int $entityId, string $entityType, bool $setValues = false, int $entryId = null): Form
    {
        $getSectionsByEntityIdAndEntityType = new GetSectionsByEntityIdAndEntityType(
            $this->sectionRepository,
            $this->sectionTranslationRepository,
            $this->fieldRepository,
            $this->localeRepository,
        );

        $sections = $getSectionsByEntityIdAndEntityType->handle($entityId, $entityType, $setValues, $entryId);

        $form = new Form(
            $sections,
        );

        $form->setEntityId($entityId);
        $form->setEntityType($entityType);

        return $form;
    }
}
