<?php

namespace Cyvian\Src\app\Classes\Translations;

use Cyvian\Src\App\Utils\Localisation;

class ActionTranslation implements \JsonSerializable
{
    public $id;
    public $labels;
    public $messages;
    public $actionLabels;
    public $actionId;

    public function __construct(Localisation $labels, ?Localisation $messages, ?Localisation $actionLabels)
    {
        $this->labels = $labels;
        $this->messages = $messages ?? Localisation::mapEmpty();
        $this->actionLabels = $actionLabels ?? Localisation::mapEmpty();
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setActionId(int $actionId)
    {
        $this->actionId = $actionId;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'labels' => $this->labels,
            'messages' => $this->messages,
            'actionLabels' => $this->actionLabels,
            'actionId' => $this->actionId
        ];
    }
}
