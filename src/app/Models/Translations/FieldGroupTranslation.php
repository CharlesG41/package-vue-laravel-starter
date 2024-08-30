<?php

namespace Cyvian\Src\App\Models\Translations;

use Cyvian\Src\App\Models\Locale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldGroupTranslation extends Model
{
    protected $table = 'field_group_translations';
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
