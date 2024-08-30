<?php

namespace Cyvian\Src\app\Classes\Translations;

use Cyvian\Src\app\Utils\Localisation;

class TabTranslation implements \JsonSerializable
{
    public $labels;
    public $tabId;

    public function __construct(Localisation $labels)
    {
        $this->labels = $labels;
    }

    public function setTabId(int $tabId)
    {
        $this->tabId = $tabId;
    }

    public function jsonSerialize(): array
    {
        return [
            'labels' => $this->labels,
            'tab_id' => $this->tabId,
        ];
    }
}
