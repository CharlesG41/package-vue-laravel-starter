<?php

namespace Cyvian\Src\app\Handlers\Section;

use Cyvian\Src\app\Repositories\SectionRepository;

class GetSectionIdsByEntityIdAndEntityType
{
    private $sectionRepository;

    public function __construct(
        SectionRepository $sectionRepository
    )
    {
        $this->sectionRepository = $sectionRepository;
    }

    public function handle(int $entityId, string $entityType): array
    {
        return $this->sectionRepository->getSectionsByEntityIdAndEntityType($entityId, $entityType)->pluck('id')->toArray();
    }
}
