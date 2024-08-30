<?php

namespace Cyvian\Src\App\Utils;

use Cyvian\Src\app\Handlers\Entry\GetEntryValuesById;
use Cyvian\Src\App\Models\Cyvian\Entry;
use Cyvian\Src\App\Models\Cyvian\News;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\EntryRepository;
use Cyvian\Src\app\Repositories\EntryTypeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;
use Cyvian\Src\app\Repositories\TabRepository;
use Cyvian\Src\app\Repositories\TabTranslationRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class QueryBuilder
{
    public function __construct(string $entryType, string $class)
    {
        $this->entry_type = $entryType;
        $this->class_to_inject = $class;
    }

    static public function instance(string $entryType, string $class): self
    {
        return new self($entryType, $class);
    }

    public function where(string $key, string $comparator, $value = null): self
    {
        if ($value === null) {
            $value = $comparator;
            $comparator = '=';
        }

        if (is_subclass_of($value, BaseModel::class)) {
            $value = $value->id;
        }
        if ($value === false) {
            $value = 0;
        }
        if ($value === true) {
            $value = 1;
        }

        $this->wheres[] = [
            'key' => $key,
            'comparator' => $comparator,
            'value' => $value,
        ];

        return $this;
    }

    public function whereNull(string $key): self
    {
        $this->wheres[] = [
            'key' => $key,
            'comparator' => '=',
            'value' => null,
        ];

        return $this;
    }

    public function only(array $keys): self
    {
        $this->onlys = $keys;

        return $this;
    }

    public function executeQuery(): Collection
    {
        $query = DB::table('entries')->select('entries.id')->distinct();
        foreach ($this->wheres as $i => $where) {
            $fieldValuesTable = 'fv' . $i;
            $fieldsTable = 'f' . $i;
            if($where['value'] === null) {
                $query = $query->join('field_values as ' . $fieldValuesTable, $fieldValuesTable . '.entry_id', '=', 'entries.id')
                    ->join('fields as ' . $fieldsTable, $fieldsTable . '.id', '=', $fieldValuesTable . '.field_id')
                    ->where($fieldsTable . '.key', $where['key'])
                    ->whereNull($fieldValuesTable . '.value');
            } else {
                $query = $query->join('field_values as ' . $fieldValuesTable, $fieldValuesTable . '.entry_id', '=', 'entries.id')
                    ->join('fields as ' . $fieldsTable, $fieldsTable . '.id', '=', $fieldValuesTable . '.field_id')
                    ->where($fieldsTable . '.key', $where['key'])
                    ->where($fieldValuesTable . '.value', $where['comparator'], $where['value']);
            }
        }
        return $query->get()->pluck('id');
    }

    public function get(bool $onlyValues = true): Collection
    {
        $ids = $this->executeQuery();

        $getEntryValuesById = new GetEntryValuesById(
            new EntryRepository,
            new EntryTypeRepository,
            new ActionRepository,
            new ActionTranslationRepository,
            new ActionEntryTypeRoleRepository,
            new FieldRepository,
            new SectionRepository,
            new SectionTranslationRepository,
            new LocaleRepository
        );

        $entries = collect();
        foreach ($ids as $id) {
            if ($onlyValues) {

                $class = $this->class_to_inject;
                $entry = new $class($getEntryValuesById->handle($id));
                $entry->id = $id;
                $entries = $entries->add($entry);
            } else {

                $entries = $entries->add($getEntryValuesById->handle($id));
            }
        }

        return $entries;
    }

    public function getEntries(): Collection
    {
        return $this->get(false);
    }

    public function update(array $data): Collection
    {
        $ids = $this->executeQuery();

        foreach ($data as $key => $value) {
            DB::table('field_values')
                ->join('entries', 'entries.id', '=', 'field_values.entry_id')
                ->join('fields', 'fields.id', '=', 'field_values.field_id')
                ->join('field_attributes', 'field_attributes.field_id', '=', 'fields.id')
                ->whereIn('entries.id', $ids)
                ->where('field_attributes.key', 'key')
                ->where('field_attributes.value', $key)
                ->update(['field_values.value' => $value]);
        }

        return $ids;
    }

    public function first()
    {
        return $this->get()->first();
    }
}
