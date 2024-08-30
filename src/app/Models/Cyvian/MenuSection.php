<?php

namespace Cyvian\Src\App\Models\Cyvian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cyvian\Src\App\Models\Cyvian\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class MenuSection extends Model
{
    use HasFactory, HasTranslations;

    protected $table = 'menu_sections';
    protected $fillable = [
        'order',
        'menu_section_id'
    ];
    public $timestamps = false;

    public function delete()
    {
        DB::beginTransaction();

        $this->entryTypes->each(function ($entryType) {
            $entryType->menu_section_id = null;
            $entryType->save();
        });

        $this->translation->each(function ($object) {
            return $object->delete();
        });

        $result = parent::delete();

        if (!$result)
            throw new \RuntimeException('Model deletion failed');

        DB::commit();

        return $result;
    }

    public function entryTypes(): HasMany
    {
        return $this->hasMany(EntryType::class);
    }
}
