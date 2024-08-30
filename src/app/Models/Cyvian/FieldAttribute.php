<?php

namespace Cyvian\Src\App\Models\Cyvian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldAttribute extends Model
{
    use HasFactory;

    protected $table = 'field_attributes';
    protected $fillable = [
        'key',
        'value',
        'field_id',
    ];
    public $timestamps = false;

    // Relationships
    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}
