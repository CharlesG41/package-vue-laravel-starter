<?php

namespace Cyvian\Src\app\Repositories;

use Cyvian\Src\App\Models\Cyvian\Translations\ActionTranslation;

class ActionTranslationRepository
{
    public function getActionTranslationByActionId(int $actionId)
    {
        return ActionTranslation::where('parent_id', $actionId)
            ->get();
    }

    public function createActionTranslation(string $label, string $message, string $actionLabel, int $actionId, int $localeId): ActionTranslation
    {
        return ActionTranslation::create([
            'label' => $label,
            'message' => $message,
            'action_label' => $actionLabel,
            'parent_id' => $actionId,
            'locale_id' => $localeId
        ]);
    }

    public function updateActionTranslation(string $label, string $message, string $actionLabel, int $actionId, int $localeId): void
    {
        ActionTranslation::where('parent_id', $actionId)
            ->where('locale_id', $localeId)
            ->update([
                'label' => $label,
                'message' => $message,
                'action_label' => $actionLabel,
            ]);
    }

    public function deleteActionTranslation(int $actionId): void
    {
        ActionTranslation::where('action_id', $actionId)
            ->delete();
    }
}
