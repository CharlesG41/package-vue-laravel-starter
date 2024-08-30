<?php

namespace Cyvian\Src\App\Models\Cyvian;

use Cyvian\Src\App\Models\Cyvian\Traits\HasTranslations;
use Cyvian\Src\App\Models\Cyvian\Translations\FolderTranslation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Folder extends Model
{
    use HasFactory, HasTranslations;

    protected $table = 'folders';
    protected $fillable = ['folder_id'];
    protected $appends = ['labels'];

    public function delete()
    {
        DB::beginTransaction();

        $this->files()->each(function ($object) {
            return $object->delete();
        });

        $this->folders->each(function ($object) {
            return $object->delete();
        });

        $result = parent::delete();

        Storage::disk($this->disk)->delete($this->path());

        if (!$result)
            throw new \RuntimeException('Model deletion failed');

        DB::commit();

        return $result;
    }

    public function getFilesAttribute(): Collection
    {
        return File::where('folder_id', $this->id)->get();
    }

    public function images(): Collection
    {
        return File::where('folder_id', $this->id)->where('image', 1)->get();
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function folders(): HasMany
    {
        return $this->hasMany(Folder::class);
    }

    public function path(): string
    {
        $path = $this->name;
        if($this->folder) {
            return $this->folder->path() . '/' . $path;
        }
        return '/' . $path . '/';
    }

    // public function getPathAttribute(): string
    // {
    //     return $this->path();
    // }

    public function getLabelAttribute() : string
    {
        return $this->translation->label;
    }

    public function getLabelsAttribute() : array
    {
        return FolderTranslation::where('parent_id', $this->id)->with('locale')
        ->get()
        ->mapWithKeys(function($translation){
            return [$translation->locale->code => $translation->label];
        })->toArray();
    }
}
