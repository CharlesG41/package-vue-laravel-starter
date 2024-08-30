<?php

namespace Cyvian\Src\app\Classes\Translations;

use Cyvian\Src\app\Utils\Localisation;

class SectionTranslation implements \JsonSerializable
{
    public $labels;
    public $sectionId;

    public function __construct(
        Localisation $labels
    )
    {
        $this->labels = $labels;
    }

    public function setSectionId(int $id)
    {
        $this->sectionId = $id;
    }

    public function jsonSerialize(): array
    {
        return [
            'labels' => $this->labels,
            'sectionId' => $this->sectionId,
        ];
    }
}
