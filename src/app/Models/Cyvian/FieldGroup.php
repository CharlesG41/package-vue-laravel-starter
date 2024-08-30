<?php

namespace Cyvian\Src\App\Models\Cyvian;

use Illuminate\Database\Eloquent\Model;
use Cyvian\Src\App\Models\Cyvian\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class FieldGroup extends Model
{
    use HasTranslations;

    protected $table = 'field_groups';
    protected $fillable = [
        'id',
    ];
    public $timestamps = false;

    public function fields(): MorphMany
    {
        return $this->morphMany(Field::class, 'entity');
    }

    public function entryTypes(): BelongsToMany
    {
        return $this->belongsToMany(EntryType::class, 'entry_type_field_group');
    }
}
