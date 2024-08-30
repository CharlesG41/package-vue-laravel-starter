<?php

namespace Cyvian\Src\App\Models\Cyvian;

use Illuminate\Database\Eloquent\Model;
use Cyvian\Src\App\Models\Cyvian\Traits\HasTranslations;

class Section extends Model
{
    use HasTranslations;

    protected $table = 'sections';
    protected $fillable = [
        'key',
        'position',
        'entity_id',
        'entity_type',
    ];
    public $timestamps = false;
}
