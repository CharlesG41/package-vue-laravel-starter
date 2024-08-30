<?php

namespace Cyvian\Src\app\Handlers\Action\ActionTranslation;

use Cyvian\Src\app\Repositories\ActionTranslationRepository;

class DeleteActionTranslation
{
    private $actionTranslationRepository;

    public function __construct(ActionTranslationRepository $actionTranslationRepository)
    {
        $this->actionTranslationRepository = $actionTranslationRepository;
    }

    public function handle(int $actionId)
    {
        $this->actionTranslationRepository->deleteActionTranslation($actionId);
    }
}
