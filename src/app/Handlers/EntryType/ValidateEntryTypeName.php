<?php

namespace Cyvian\Src\app\Handlers\EntryType;

use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Repositories\EntryTypeRepository;

class ValidateEntryTypeName
{
    private $entryTypeRepository;

    public function __construct(EntryTypeRepository $entryTypeRepository)
    {
        $this->entryTypeRepository = $entryTypeRepository;
    }
    // returns true if the name is valid
    public function handle(string $entryTypeName, array $exceptIds = []): bool
    {
        $entryType = $this->entryTypeRepository->getEntryTypeByName($entryTypeName);
        if ($entryType) {
            if (in_array($entryType->id, $exceptIds)) {
                return true;
            }
        }

        return !($entryType);
    }
}
