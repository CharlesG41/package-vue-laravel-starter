<?php

namespace Cyvian\Src\App\Models\Cyvian\Translations;

use Cyvian\Src\App\Models\Cyvian\Locale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectionTranslation extends Model
{
    protected $table = 'section_translations';
    protected $fillable = [
        'label',
        'parent_id',
        'locale_id',
    ];
    public $timestamps = false;
}
