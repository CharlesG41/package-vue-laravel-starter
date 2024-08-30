<?php

namespace Cyvian\Src\App\Models\Cyvian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Cyvian\Src\App\Models\Cyvian\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

class Action extends Model
{
    use HasFactory, HasTranslations;

    protected $table = 'actions';
    protected $fillable = [
        'name',
        'position',
        'url',
        'action_type',
        'roles_by_entry',
        'entry_type_id',
    ];
    public $timestamps = false;
    public $with = ['translation'];

    public const ACTION_TYPE_VIEW = 'view';
    public const ACTION_TYPE_EXECUTE = 'execute';
    public const ACTION_TYPE_DOWNLOAD = 'download';
    public const POSITION_TOP = 'top';
    public const POSITION_ROW = 'row';
    public const POSITION_GENERAL = 'general';

    public function delete()
    {
        DB::beginTransaction();

        $this->translation()->delete();

        $this->tabs->each(function ($object) {
            return $object->delete();
        });

        $this->fields->each(function ($object) {
            return $object->delete();
        });

        DB::table('action_entry_role')->where('action_id', $this->id)->delete();
        DB::table('action_entry_type_role')->where('action_id', $this->id)->delete();

        $result = parent::delete();

        if (!$result)
            throw new \RuntimeException('Model deletion failed');

        DB::commit();

        return $result;
    }

    public function entryType(): BelongsTo
    {
        return $this->belongsTo(EntryType::class);
    }

    public function tabs(): MorphMany
    {
        return $this->morphMany(Tab::class, 'entity');
    }

    public function fields(): MorphMany
    {
        return $this->morphMany(Field::class, 'entity');
    }

    public function roleIds(): array
    {
        if ($this->roles_by_entry) {
            return DB::table('action_entry_role')->select('role_id')->where('action_id', $this->id)->get()->pluck('role_id')->toArray();
        } else {
            return DB::table('action_entry_type_role')->select('role_id')->where('action_id', $this->id)->get()->pluck('role_id')->toArray();
        }
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'icon' => $this->icon,
            'label' => $this->translation->label,
            'action_type' => $this->action_type,
            'url' => $this->url,
            'fields' => $this->fields,
            'tabs' => $this->tabs,
            'message' => $this->translation->message,
            'action_label' => $this->translation->action_label,
        ];
    }

    public function addPermissions(array $roleIds, int $entryId = null): void
    {
        if ($this->roles_by_entry) {
            DB::table('action_entry_role')->where('action_id', $this->id)->delete();
            $rows = [];
            foreach ($roleIds as $roleId) {
                $rows[] = ['action_id' => $this->id, 'entry_id' => $entryId, $roleId];
            }
            DB::table('action_entry_role')->insert($rows);
        } else {
            DB::table('action_entry_role')->where('action_id', $this->id)->delete();
            $rows = [];
            foreach ($roleIds as $roleId) {
                $rows[] = ['action_id' => $this->id, 'entry_id' => $this->entryType->id, $roleId];
            }
            DB::table('action_entry_type_role')->insert($rows);
        }
    }

    public function canBeExecuted(array $roleIds, int $entryId = null): bool
    {
        if ($this->roles_by_entry) {
            if ($entryId === null) {
                abort(422, __('cyvian.exceptions.no_entry_found'));
            }
            $results = DB::table('action_entry_role')
                ->select('*')
                ->where('action_id', $this->id)
                ->where('entry_id', $entryId)
                ->whereIn('role_id', $roleIds)
                ->get();

            return count($results) > 0;
        } else {
            $results = DB::table('action_entry_type_role')
                ->select('*')
                ->where('action_id', $this->id)
                ->where('entry_type_id', $this->entryType->id)
                ->whereIn('role_id', $roleIds)
                ->get();

            return count($results) > 0;
        }
    }
}
