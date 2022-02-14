<?php

namespace App\Http\Controllers;

use App\Models\AudioParent;
use Illuminate\Http\Request;

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

        return response()->json([
            'status' => true,
            'data' => [
                'audioParents' => AudioParent::orderBy('id', 'desc')->limit($limit)->offset(($page - 1) * $limit)->get(),
            ]
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
            $coverImgFilePath = is_null($request->coverImg) ?  null : $request->file('coverImg')->store('audioParentCoverImgs', 'public');

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

        $this->request = $request;
        $this->audioParent = $audioParent;
        $this->cover_img_path = is_null($request->coverImg) ?  $audioParent->cover_img_path : $request->file('coverImg')->store('audioParentCoverImgs', 'public');
        $this->data = [
            'name' => is_null($request->name) ? $audioParent->name : $request->name,
        ];

        try {
            $this->$audioParent = AudioParent::where('id', $this->audioParent->id)->update($this->data);
        } catch (\Exception $e) {
            return response()->json([
                'updated' => false,
                'error' => $e->getMessage()
            ]);
        }
        return response()->json([
            'updated' => true,
            'data' => [
                'audioParent' => AudioParent::where('id', $this->audioParent->id)->first()
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
