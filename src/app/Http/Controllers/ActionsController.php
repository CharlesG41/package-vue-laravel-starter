<?php

namespace Cyvian\Src\App\Http\Controllers;

use App\Managers\ActionResponse;
use App\Managers\Manager;
use Cyvian\Src\app\Handlers\Action\GetActionByNameFromEntryType;
use Cyvian\Src\App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActionsController extends Controller
{
    public function execute(Request $request, string $entryType, string $actionName)
    {
        $getActionByNameFromEntryType = new GetActionByNameFromEntryType;

        $manager = Manager::getManagerObject($entryType);
        $action = $getActionByNameFromEntryType->handle($actionName, $manager->entryType);

        if ($action === null) {
            return $this->setActionResponseInSessionAndBack(new ActionResponse(__('cyvian.errors.does_not_exists'), 'error'));
        }

        // check if the id is valid and if user has the roles to execute the action
        if ($request->exists('id') && $request->input('id') !== null) {
            if (!Auth::user()->can($actionName, $manager->entryType, $request->input('id'))) {
                return $this->setActionResponseInSessionAndBack(new ActionResponse(__('cyvian.errors.forbidden'), 'error'));
            }
        } else {
            if (!Auth::user()->can($actionName, $manager->entryType)) {
                return $this->setActionResponseInSessionAndBack(new ActionResponse(__('cyvian.errors.forbidden'), 'error'));
            }
        }

        // check if fields are valid
        if (is_array($request->input('fields')) ?? count($request->input('fields')) > 0) {
            $errors = $manager->validateForm($request->input('fields'));
            if (!empty($errors)) {
                return $this->setActionResponseInSessionAndBack(new ActionResponse(__('cyvian.errors.validation_failed'), 'error'));
            }
        }

        $response = $manager->{Helper::snakeToCamel($actionName)}($request);

        return $this->setActionResponseInSessionAndBack($response);
    }
}
