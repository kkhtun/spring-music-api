<?php

namespace App\Http\Controllers;

use App\Http\Requests\VersionRequest;
use App\Models\Version;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    public function index(VersionRequest $request)
    {
        $page = $request->has('page') ? (int) $request->get('page') : 1;
        $limit = $request->has('limit') ? (int) $request->get('limit') : 10;

        try {
            $query = new Version;
            $versions = $query->orderBy('id', 'desc')->limit($limit)->offset(($page - 1) * $limit)->get();
            $count = $query->count();
            return response()->json([
                "data" => $versions,
                "count" => $count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage(),
                "code" => $e->getCode(),
            ]);
        }
    }

    public function show(Version $version)
    {
        return response()->json([
            'data' => $version
        ]);
    }

    public function store(VersionRequest $request)
    {
        try {
            $version = new Version;
            $version->name = $request->name;
            $version->playstore_url = $request->playstore_url;
            $version->remark = $request->remark;
            $version->save();

            return response()->json($version, 201);
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage(),
                "code" => $e->getCode()
            ], 500);
        }
    }

    public function update(VersionRequest $request, Version $version)
    {
        try {
            $updated = Version::where('id', $version->id)->update($request->only(['name', 'playstore_url', 'remark']));
            return response()->json([
                "status" => $updated ? true : false
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage(),
                "code" => $e->getCode()
            ], 500);
        }
    }

    public function destroy(Version $version)
    {
        try {
            $deleted = $version->delete();
            return response()->noContent();
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage(),
                "code" => $e->getCode()
            ], 500);
        }
    }
}
