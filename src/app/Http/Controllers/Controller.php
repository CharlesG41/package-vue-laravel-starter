<?php

namespace Cyvian\Src\App\Http\Controllers;

use App\Managers\ActionResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as LaravelController;

class Controller extends LaravelController
{
    protected function success(array $array, int $code = 200): JsonResponse
    {
        return response()->json($array, $code);
    }

    protected function fail(array $array, int $code = 500): JsonResponse
    {
        return response()->json($array, $code);
    }

    protected function setActionResponseInSessionAndBack(ActionResponse $actionResponse)
    {
        session(['action_response' => serialize($actionResponse)]);

        return redirect()->back();
    }
}
