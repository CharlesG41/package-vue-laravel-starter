<?php

namespace Cvyian\Src\App\Handlers\Folder;

use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Handlers\HandlerResponse;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\App\Models\Cyvian\Folder;
use Cyvian\Src\App\Repositories\FolderRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;

class UpdateFolder extends BaseFolder
{
    private $folderRepository;
    private $localeRepository;

    const ERROR_MISSING_LABEL = 'error_missing_label';
    const ERROR_NOT_UNIQUE_NAME = 'error_not_unique_name';

    public function __construct(FolderRepository $folderRepository, LocaleRepository $localeRepository)
    {
        $this->folderRepository = $folderRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(Folder $folder, array $labels): HandlerResponse
    {
        $getLocalesByType = new GetLocalesByType($this->localeRepository);
        $localesCms = $getLocalesByType->handle(Locale::IS_CMS);

        foreach ($localesCms as $locale) {
            if (!key_exists($locale->code, $labels) || !$labels[$locale->code]) {
                return new HandlerResponse(self::ERROR_MISSING_LABEL, false);
            }
        }

        if (!$this->isFolderNameValid($labels, $folder->folder_id, $folder->id)) {
            return new HandlerResponse(self::ERROR_NOT_UNIQUE_NAME, false);
        }

        $folder = $this->folderRepository->updateFolder($folder->id, $labels);

        return new HandlerResponse($folder, true);
    }
}
