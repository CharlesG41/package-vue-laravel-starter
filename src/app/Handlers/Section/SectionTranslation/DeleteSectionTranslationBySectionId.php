<?php

namespace Cyvian\Src\app\Handlers\Section\SectionTranslation;

use Cyvian\Src\app\Repositories\SectionTranslationRepository;

class DeleteSectionTranslationBySectionId
{
    private $sectionTranslationRepository;

    public function __construct(
        SectionTranslationRepository $sectionTranslationRepository
    )
    {
        $this->sectionTranslationRepository = $sectionTranslationRepository;
    }

    public function handle(int $sectionId)
    {
        $this->sectionTranslationRepository->deleteSectionTranslationBySectionId($sectionId);
    }
}
