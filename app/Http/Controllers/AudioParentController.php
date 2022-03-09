<?php

namespace App\Http\Controllers;

use App\Models\AudioParent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AudioParentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 10;
        $minified = (bool) $request->get('minified');

        $query = AudioParent::orderBy('id', 'desc');

        if ($minified) {
            $data = $query->get(['name', 'id']);
            $count = $query->count();
        } else {
            $data = $query->limit($limit)->offset(($page - 1) * $limit)->get();
            $count = $query->count();
        }

        return response()->json([
            'status' => true,
            'data' => [
                'audioParents' => $data
            ],
            "count" => $count
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|max:255',
                'coverImg' => 'nullable|file'
            ]);
            $coverImgFilePath = is_null($request->coverImg) ?  null : $request->file('coverImg')->store('audioParentCoverImgs', 's3');

            $audioParent = new AudioParent();
            $audioParent->name = $request->name;
            $audioParent->cover_img_path = $coverImgFilePath;
            $audioParent->save();

            return response()->json([
                'created' => true,
                'data' => [
                    'audioParent' => $audioParent
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'created' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AudioParent  $audioParent
     * @return \Illuminate\Http\Response
     */
    public function show(AudioParent $audioParent)
    {
        return response()->json([
            'status' => true,
            'data' => [
                'audioParent' => $audioParent,
                'audios' => $audioParent->audios
            ]
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AudioParent  $audioParent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AudioParent $audioParent)
    {
        $cover_img_path = null;
        if ($request->coverImg) {
            $cover_img_path = $request->file('coverImg')->store('audioParentCoverImgs', 's3');
            Storage::disk("s3")->delete($audioParent->cover_img_path);
        } else {
            $cover_img_path = $audioParent->cover_img_path;
        }

        $data = [
            'name' => is_null($request->name) ? $audioParent->name : $request->name,
            'cover_img_path' => $cover_img_path
        ];

        try {
            $res = AudioParent::where('id', $audioParent->id)->update($data);
        } catch (\Exception $e) {
            return response()->json([
                'updated' => false,
                'error' => $e->getMessage()
            ]);
        }
        return response()->json([
            'updated' => true,
            'data' => [
                'audioParent' =>
                AudioParent::find($audioParent->id)
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AudioParent  $audioParent
     * @return \Illuminate\Http\Response
     */
    public function destroy(AudioParent $audioParent)
    {
        if ($audioParent->cover_img_path) {
            Storage::disk("s3")->delete($audioParent->cover_img_path);
        }

        try {
            $audioParent->delete();
            return response()->json([
                'deleted' => true,
                'data' => [
                    'audioParent' => $audioParent
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'deleted' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
