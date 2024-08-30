<?php

namespace Cyvian\Src\App\Models\Cyvian;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Cyvian\Src\App\Models\Cyvian\Traits\HasTranslations;

class Tab extends Model
{
    use HasTranslations;

    protected $table = 'tabs';
    protected $fillable = [
        'entity_id',
        'entity_type',
    ];
    public $timestamps = false;

    // relationships
    public function entryType(): BelongsTo
    {
        return $this->morphTo();
    }

    public function actions(): BelongsTo
    {
        return $this->morphTo();
    }

    public function entry(): BelongsTo
    {
        return $this->morphTo();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->translation->label,
        ];
    }
}
