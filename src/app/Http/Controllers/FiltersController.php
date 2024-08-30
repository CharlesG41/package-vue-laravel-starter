<?php

namespace Cyvian\Src\App\Http\Controllers;

use Cyvian\Src\App\Models\Cyvian\EntryType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FiltersController extends Controller
{
    // public function execute(Request $request, string $entryType, string $filterName): JsonResponse
    // {
    //     $entryType = EntryType::where('name', $entryType)->get()->first();
    //     $manager = $this->getManagerObject($entryType->name);
    //     $filter = $entryType->filters()->where('name', $filterName)->get()->first();

    //     if ($filter === null) {
    //         return parent::fail(['message' => 'cyvian.filters.does_not_exists'], 403);
    //     }

    //     $response = $manager->{$filterName}($request);

    //     return $response['success'] ? parent::success($response) : parent::fail($response);
    // }
}
