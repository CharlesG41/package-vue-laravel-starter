<?php

namespace Cyvian\Src\App\Models\Cyvian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FieldValue extends Model
{
    use HasFactory;

    private $_field_attributes;

    protected $table = 'field_values';
    protected $fillable = [
        'value',
        'field_id',
        'field_value_id',
        'locale_id',
        'entry_id',
    ];
    public $timestamps = false;
}
