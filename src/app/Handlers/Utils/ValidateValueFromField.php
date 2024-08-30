<?php

namespace Cyvian\Src\app\Handlers\Utils;

use Cyvian\Src\app\Classes\Fields\Classes\BaseField;
use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Handlers\HandlerResponse;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Repositories\LocaleRepository;

class ValidateValueFromField
{
    public function handle(BaseField &$field, array $fields, array $locales = [])
    {
        if (empty($locales)) {
            $getLocalesByType = new GetLocalesByType(new LocaleRepository);
            $locales = $getLocalesByType->handle(Locale::IS_SITE);
        }
        $validationStore = ValidationStore::getInstance();

        if ($field->translatable) {
            $error = [];
            foreach ($locales as $locale) {
                if(!$this->fieldNeedsToBeValidated($field, $fields, $locale->code)) {
                    continue;
                }
                if (!array_key_exists($locale->code, $field->value ?? [])) {
//                    dd('Value for locale ' . $locale->code . ' is not set on field "' . $field->key . '".', $field, $fields);
                    throw new \Exception('Value for locale ' . $locale->code . ' is not set on field "' . $field->key . '".');
                }
                if (!$field->isValid($field->value[$locale->code])) {
                    $error[$locale->code] = $field->error;
                    $validationStore->hasError = true;
                    $validationStore->addLocaleAffected($locale->code);
                }
                $field->error = $error;
            }
            $field->error = $error;
        } else {
            if($this->fieldNeedsToBeValidated($field, $fields) && !$field->isValid($field->value)) {
                $validationStore->hasError = true;
            };
        }
    }

    private function fieldNeedsToBeValidated(BaseField $field, array $fields, $langCode = null): bool
    {
        if (count($field->conditions) > 0) {
            foreach ($field->conditions as $condition) {
                if (!$condition->conditionIsMet($fields, $langCode)) {
                    return false;
                }
            }
        }
        return true;
    }
}
