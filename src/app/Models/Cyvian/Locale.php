<?php

namespace Cyvian\Src\App\Models\Cyvian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Locale extends Model
{
    use HasFactory;

    protected $table = 'locales';
    protected $fillable = [
        'name',
        'code',
        'is_cms',
        'is_site',
        'is_default'
    ];
    public $timestamps = false;

    public function delete()
    {
        DB::beginTransaction();

        $result = parent::delete();

        if (!$result)
            throw new \RuntimeException('Model deletion failed');

        DB::commit();

        return $result;
    }
}
