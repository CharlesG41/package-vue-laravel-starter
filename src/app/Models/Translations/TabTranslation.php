<?php

namespace Cyvian\Src\App\Models\Translations;

use Cyvian\Src\App\Models\Locale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TabTranslation extends Model
{
    protected $table = 'tab_translations';
    protected $fillable = [
        'label',
        'parent_id',
        'locale_id',
    ];
    public $timestamps = false;

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }
}
