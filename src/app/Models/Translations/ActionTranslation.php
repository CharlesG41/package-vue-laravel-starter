<?php

namespace Cyvian\Src\App\Models\Translations;

use Cyvian\Src\App\Models\Locale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionTranslation extends Model
{
    protected $table = 'action_translations';
    protected $fillable = [
        'label',
        'action_label',
        'message',
        'parent_id',
        'locale_id',
    ];
    public $timestamps = false;

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }
}
