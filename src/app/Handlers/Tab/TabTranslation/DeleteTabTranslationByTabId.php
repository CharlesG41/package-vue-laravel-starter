<?php

namespace Cyvian\Src\app\Handlers\Tab\TabTranslation;

use Cyvian\Src\app\Repositories\TabTranslationRepository;

class DeleteTabTranslationByTabId
{
    private $tabTranslationRepository;

    public function __construct(TabTranslationRepository $tabTranslationRepository)
    {
        $this->tabTranslationRepository = $tabTranslationRepository;
    }

    public function handle(int $tabId)
    {
        $this->tabTranslationRepository->deleteTabTranslationByTabId($tabId);
    }
}
