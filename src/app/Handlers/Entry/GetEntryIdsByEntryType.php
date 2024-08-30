<?php

namespace Cyvian\Src\app\Handlers\Entry;

use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Repositories\EntryRepository;

class GetEntryIdsByEntryType
{
    private $entryRepository;

    public function __construct(EntryRepository $entryRepository)
    {
        $this->entryRepository = $entryRepository;
    }

    public function handle(EntryType $entryType): array
    {
        return $this->entryRepository->getAllEntriesByEntryTypeId($entryType->id)->pluck('id')->toArray();
    }
}
