<?php

namespace Cyvian\Src\app\Classes\Fields\Classes;

class Condition implements \JsonSerializable
{
    public $fieldKey;
    public $operator;
    public $value;

    public function __construct(
        string $fieldKey,
        string $operator,
        string $value
    )
    {
        $this->fieldKey = $fieldKey;
        $this->operator = $operator;
        $this->value = $value;
    }

    public function conditionIsMet(array $fields, ?string $langCode): bool
    {
        $parentFields = array_values(
            array_filter($fields, function ($f) use ($fields) {
                if (!property_exists($f, 'key')) {
                    dd('condition.php', $fields);
                }
                return $this->fieldKey === $f->key;
            })
        );

        if (empty($parentFields)) {
            throw new \Exception('Parent field not found on condition ' . json_encode($this));
        } else {
            $parentField = $parentFields[0];
        }
        $parentValue = $parentField->value;

        if ($parentField->translatable) {
            $parentValue = $parentValue[$langCode];
        }

        if ($this->operator == "=" && $parentValue == $this->value) {
            return true;
        }
        if ($this->operator == ">" && $parentValue > $this->value) {
            return true;
        }
        if ($this->operator == "<" && $parentValue < $this->value) {
            return true;
        }
        if ($this->operator == "!=" && $parentValue != $this->value) {
            return true;
        }

        return false;
    }

    public function jsonSerialize(): array
    {
        return [
            'field' => $this->fieldKey,
            'operator' => $this->operator,
            'value' => $this->value
        ];
    }
}
