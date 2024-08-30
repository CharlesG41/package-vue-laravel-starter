<?php

namespace Cyvian\Src\App\Models\Cyvian;

use Cyvian\Src\App\Models\Cyvian\Traits\HasTranslations;
use Cyvian\Src\App\Models\Cyvian\Translations\FileTranslation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use JsonSerializable;

class File extends Model implements JsonSerializable
{
    use HasTranslations;
    protected $table = 'files';

    protected $fillable = [
        'name',
        'extension',
        'disk',
        'is_image',
        'folder_id',
    ];

    public function delete(): bool
    {
        Storage::disk($this->disk)->delete($this->fullName);

        return parent::delete();
    }

    public function deleteFile() : bool
    {
        Storage::disk($this->disk)->delete('/'.$this->fullName);

        return true;
    }

    public function getPathAttribute(): string
    {
        if (property_exists($this, 'folder_id') && $this->folder_id !== null) {
            return Folder::find($this->folder_id)->path() . '/';
        } else {
            return '/';
        }
    }

    public function getFullPathAttribute() : string
    {
        return $this->path . $this->fullName;
    }

    public function getFullNameAttribute() : string 
    {
        return $this->name . '.' . $this->extension;
    }

    public function jsonSerialize() : array
    {
        $descriptions = FileTranslation::where('parent_id', $this->id)->with('locale')
        ->get()
        ->mapWithKeys(function($translation){
            return [$translation->locale->code => $translation->description];
        })->toArray();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'fullName' => $this->fullname,
            'extension' => $this->extension,
            'is_image' => $this->is_image,
            'description' => $descriptions,
            'url' => Storage::disk($this->disk)->url($this->fullPath),
            'folder_id' => $this->folder_id
        ];
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }
}
