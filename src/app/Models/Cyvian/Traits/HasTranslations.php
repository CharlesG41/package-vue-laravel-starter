<?php

namespace Cyvian\Src\App\Models\Cyvian\Traits;

use Cyvian\Src\App\Models\Cyvian\Locale;
use Cyvian\Src\App\Utils\Constant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasTranslations
{
    public function translations(): HasMany
    {
        $class = get_class($this);
        $class = explode('\\', $class);
        $class = end($class);

        return $this->hasMany(Constant::CLASS_NAME_CYVIAN_SRC_APP_MODELS_CYVIAN . 'Translations\\' . $class . 'Translation', 'parent_id');
    }

    public function translation(): HasOne
    {
        $class = get_class($this);
        $class = explode('\\', $class);
        $class = end($class);
        $locale = config('locales.current_locale');

        return $this->hasOne(Constant::CLASS_NAME_CYVIAN_SRC_APP_MODELS_CYVIAN . 'Translations\\' . $class . 'Translation', 'parent_id')->where('locale_id', $locale->id);
    }

    static public function whereTranslations(string $name, $data): Builder
    {
        $class = end($class) . 'Translation';
        return $class::where($name, $data);
    }
}
