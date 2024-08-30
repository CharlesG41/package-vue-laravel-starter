<?php

namespace Cyvian\Src\App\Models\Cyvian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class Field extends Model
{
    use HasFactory;

    public $_field_attributes;

    protected $table = 'fields';
    protected $fillable = [
        'key',
        'type',
        'name',
        'description',
        'width',
        'translatable',
        'display_on_list',
        'has_filter',
        'locked',
        'conditions',
        'hidden_on_create',
        'hidden_on_edit',
        'disabled_on_edit',
        'roles_on_create',
        'roles_on_edit_or_disable',
        'roles_on_edit_or_hide',
        'is_base_field',
        'entry_id',
        'entity_id',
        'entity_type',
    ];
    public $timestamps = false;


    // Relationships
//    public function parent(): MorphTo
//    {
//        return $this->morphTo('parent', 'entity_type', 'entity_id');
//    }
//
//    public function fields(): MorphMany
//    {
//        return $this->morphMany(self::class, 'parent', 'entity_type', 'entity_id');
//    }
//
//    public function value(): HasMany
//    {
//        return $this->hasMany(FieldValue::class);
//    }

//    static public function whereAttribute(int $entryId, string $attributeKey, $value, bool $includeBaseFields = true)
//    {
//        if ($value === true) {
//            $value = 1;
//        } elseif ($value === false) {
//            $value = 0;
//        }
//
//        return DB::table('fields')
//            ->select('fields.id')
//            ->join('field_attributes', 'field_attributes.field_id', '=', 'fields.id')
//            ->where('field_attributes.key', $attributeKey)
//            ->where('field_attributes.value', $value)
//            ->get()
//            ->map(function ($result) {
//                return Field::find($result->id);
//            });
//    }
}
