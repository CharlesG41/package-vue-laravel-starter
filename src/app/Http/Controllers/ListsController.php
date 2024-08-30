<?php

namespace Cyvian\Src\App\Http\Controllers;

use App\Managers\Manager;
use Cyvian\Src\App\Models\Cyvian\Entry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListsController extends Controller
{
    public function rowById(Request $request, int $entryId): JsonResponse
    {
        $entry = Entry::find($entryId);
        $manager = Manager::getManagerObject($entry->entryType);
        if ($entry === null) {
            return parent::success([
                'row' =>  null
            ]);
        } else {
            $locale = config('locales.current_locale');
            return parent::success([
                'row' => $manager->row($entry, $locale->id),
            ]);
        }
    }
}
