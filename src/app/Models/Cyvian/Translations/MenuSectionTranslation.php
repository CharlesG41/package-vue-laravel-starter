<?php

namespace Cyvian\Src\App\Models\Cyvian\Translations;

use Cyvian\Src\App\Models\Cyvian\Locale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuSectionTranslation extends Model
{
    protected $table = 'menu_section_translations';
    protected $fillable = [
        'name',
        'parent_id',
        'locale_id',
    ];
    public $timestamps = false;

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }
}
