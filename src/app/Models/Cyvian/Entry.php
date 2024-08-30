<?php

namespace Cyvian\Src\App\Models\Cyvian;

use Cyvian\Src\App\Utils\FieldHelper;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

class Entry extends Model
{
    use HasFactory, Authenticatable;

    protected $table = 'entries';
    protected $fillable = [
        'created_by',
        'updated_by',
        'entry_type_id',
        'order',
    ];
    protected $appends = ['values'];

    public function delete()
    {
        DB::beginTransaction();

        $this->fields->each(function ($field) {
            return $field->delete();
        });
        $this->fieldValues->each(function ($fieldValue) {
            return $fieldValue->delete();
        });
        $this->tabs->each(function ($tab) {
            return $tab->delete();
        });

        // DB::table('action_entry_role')->where('entry_id', $this->id)->delete();
        // todo remove entry on one_entry and many_entries fields

        $result = parent::delete();

        if (!$result)
            throw new \RuntimeException('Model deletion failed');

        DB::commit();

        return $result;
    }

    // Relationships
    public function fields(): MorphMany
    {
        return $this->morphMany(Field::class, 'parent', 'entity_type', 'entity_id');
    }

    public function tabs(): MorphMany
    {
        return $this->morphMany(Tab::class, 'entity');
    }

    public function entryType(): BelongsTo
    {
        return $this->belongsTo(EntryType::class);
    }

    public function fieldValues(): HasMany
    {
        return $this->hasMany(FieldValue::class);
    }

    public function getValuesAttribute(): array
    {
        $values = [
            'id' => $this->id
        ];
        $locale = config('locales.current_locale');
        $fieldValues = FieldValue::where('entry_id', $this->id)->where('locale_id', $locale->id)->whereNull('field_value_id')->orWhereNull('locale_id')->where('entry_id', $this->id)->whereNull('field_value_id')->get();
        foreach ($fieldValues as $fieldValue) {
            $type = FieldHelper::getFieldClass($fieldValue->type);
            $key = $fieldValue->key;
            $values[$key] = $type::value($this, $fieldValue);
        }

        return $values;
    }

    public function getValuesWithTranslationsAttribute(): array
    {
        $values = [];
        $fieldValues = FieldValue::where('entry_id', $this->id)->whereNull('field_value_id')->get();

        foreach ($fieldValues as $fieldValue) {
            $type = FieldHelper::getFieldClass($fieldValue->type);
            $key = $fieldValue->key;
            $values[$key] = $type::valueWithTranslations($this, $fieldValue);
        }

        return $values;
    }

    public function getMainValueAttribute()
    {
        $field = $this->entryType->fields->first();
        $values = $this->values_with_translations;
        return $field->translatable ? $values[$field->key][session('locale') ?? 'fr'] : $values[$field->key];
    }

    static public function hasQuickEdit(): bool
    {
        return true;
    }
}
