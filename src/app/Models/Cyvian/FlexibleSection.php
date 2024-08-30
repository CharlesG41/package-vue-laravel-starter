<?php

namespace Cyvian\Src\app\Models\Cyvian;

use Illuminate\Database\Eloquent\Model;

class FlexibleSection extends Model
{
    protected $table = 'flexible_sections';
    protected $fillable = [
        'key',
        'labels',
        'field_id',
    ];
    public $timestamps = false;
}
