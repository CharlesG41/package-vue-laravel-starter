<?php

namespace Cyvian\Src\app\Handlers\Tab;

use Cyvian\Src\app\Repositories\TabRepository;

class GetTabIdsByEntityIdAndEntityType
{
    private $tabRepository;

    public function __construct(
        TabRepository $tabRepository
    )
    {
        $this->tabRepository = $tabRepository;
    }

    public function handle(int $entityId, string $entityType): array
    {
        return $this->tabRepository->getTabsByEntityIdAndEntityType($entityId, $entityType)->pluck('id')->toArray();
    }
}
