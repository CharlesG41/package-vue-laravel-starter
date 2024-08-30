<?php

namespace Cyvian\Src\App\Http\Controllers;

use App\Managers\ActionResponse;
use App\Managers\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormsController extends Controller
{
    public function store(Request $request, string $entryType)
    {
        $manager = Manager::getManagerObject($entryType);

        if (!Auth::user()->can('create', $manager->entryType)) {
            return $this->setActionResponseInSessionAndBack(
                new ActionResponse('cyvian.messages.forbidden', 'error')
            );
        }

        $actionResponse = $manager->store($request);

        if ($actionResponse->isSuccess()) {
            session(['action_response' => serialize($actionResponse)]);

            return redirect($manager->getListUrl());
        } else {
            return $this->setActionResponseInSessionAndBack($actionResponse);
        }
    }

    public function update(Request $request, string $entryType, int $id)
    {
        $manager = Manager::getManagerObject($entryType);
        $entry = $manager->getEntryById($id);

        if ($entry === null) {
            return $this->setActionResponseInSessionAndBack(
                new ActionResponse('cyvian.messages.not_found', 'error')
            );
        }

        if (!Auth::user()->can('edit', $manager->entryType, $entry->id)) {
            return $this->setActionResponseInSessionAndBack(
                new ActionResponse('cyvian.messages.forbidden', 'error')
            );
        }

        $actionResponse = $manager->update($request, $id);

        if ($actionResponse->isSuccess()) {
            session(['action_response' => serialize($actionResponse)]);
            return redirect($manager->getListUrl());
        }

        return $this->setActionResponseInSessionAndBack($actionResponse);
    }
}
