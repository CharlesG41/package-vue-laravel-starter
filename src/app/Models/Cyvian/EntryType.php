<?php

namespace Cyvian\Src\App\Models\Cyvian;

use Cyvian\Src\App\Models\Cyvian\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

class EntryType extends Model
{
    use HasFactory, HasTranslations;

    const TYPE_MODEL = 'model';
    const TYPE_SETTING = 'setting';

    protected $table = 'entry_types';
    protected $fillable = [
        'name',
        'type',
        'menu_section_id'
    ];

    public function delete()
    {
        DB::beginTransaction();

        $this->tabs->each(function ($tab) {
            return $tab->delete();
        });
        $this->actions->each(function ($object) {
            return $object->delete();
        });
        $this->fields->each(function ($object) {
            return $object->delete();
        });

        $this->entries->each(function ($object) {
            return $object->delete();
        });

        $this->fieldGroups->each(function ($object) {
            return $object->delete();
        });


        // todo remove entry_types id on one_entry and many_entries fields

        $result = parent::delete();

        if (!$result)
            throw new \RuntimeException('Model deletion failed');

        DB::commit();

        return $result;
    }

    // Relationships
    public function fields(): MorphMany
    {
        return $this->morphMany(Field::class, 'entity');
    }

    public function fieldGroups(): BelongsToMany
    {
        return $this->belongsToMany(FieldGroup::class, 'entry_type_field_group');
    }

    public function tabs(): MorphMany
    {
        return $this->morphMany(Tab::class, 'entity');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(Action::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }

    public function menuSection(): BelongsTo
    {
        return $this->belongsTo(MenuSection::class);
    }
}
