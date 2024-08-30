<?php

namespace Cyvian\Src\App\Models\Translations;

use Cyvian\Src\App\Models\Locale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntryTypeTranslation extends Model
{
    protected $table = 'entry_type_translations';
    protected $fillable = [
        'singular_name',
        'plural_name',
        'parent_id',
        'locale_id',
    ];
    public $timestamps = false;

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }
}
