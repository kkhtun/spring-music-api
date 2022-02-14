<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AudioController extends Controller
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
        $audios = Audio::orderBy('id', 'desc')->limit($limit)->offset(($page - 1) * $limit)->get();
        $results = [];
        foreach ($audios as $audio) {
            array_push($results, [
                "audio" => $audio,
                "audioParents" => $audio->audioParents,
                "categories" => $audio->categories,

            ]);
        }
        array_push($results, ['count' => $limit]);

        return response()->json([
            'status' => true,
            'data' => [
                'audios' => $results
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
                'title' => 'required|max:255',
                'artWorkFile' => 'nullable',
                'audioFile' => 'required|file',
                'categories.*' => 'distinct',
                'audioParents.*' => 'distinct',
            ]);

            $this->request = $request;
            $this->artWorkFilePath = is_null($request->artWorkFile) ?  null : $this->request->file('artWorkFile')->store('artWorkFiles', 'public');
            $this->audioFilePath = $this->request->file('audioFile')->store('audioFiles', 'public');
            $this->categories = $this->request->categories;
            $this->audioParents = $this->request->audioParents;
            DB::transaction(function () {
                $audio = new Audio();
                $audio->title = $this->request->title;
                $audio->art_work_file_path = $this->artWorkFilePath;
                $audio->audio_file_path = $this->audioFilePath;
                $audio->save();
                $audio->categories()->sync(json_decode($this->request->categories, true));
                $audio->audioParents()->sync(json_decode($this->request->audioParents, true));
                $this->createdAudio = $audio;
            });

            return response()->json([
                'created' => true,
                'data' => [
                    'audio' => $this->createdAudio,
                    'audioParent' => $this->createdAudio->audioParents,
                    'category' => $this->createdAudio->categories
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
     * @param  \App\Models\Audio  $audio
     * @return \Illuminate\Http\Response
     */
    public function show(Audio $audio)
    {
        return response()->json([
            'status' => true,
            'data' => [
                'audio' => $audio,
                'audioParent' => $audio->audioParents,
                'category' => $audio->categories
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Audio  $audio
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Audio $audio)
    {

        $this->audio = $audio;
        $this->request = $request;
        $this->artWorkFilePath = is_null($request->artWorkFile) ?  $audio->art_work_file_path : $this->request->file('artWorkFile')->store('artWorkFiles', 'public');
        $this->audioFilePath = is_null($request->audioFile) ? $audio->audio_file_path : $this->request->file('audioFile')->store('audioFiles', 'public');
        $this->data = [
            'title' => is_null($this->request->title) ? $this->audio->title : $this->request->title,
            'art_work_file_path' => $this->artWorkFilePath,
            'audio_file_path' => $this->audioFilePath,
        ];

        try {
            DB::transaction(function () {

                Audio::where('id', $this->audio->id)->update($this->data);
                // Storage::delete('file.jpg');
                $this->audio->categories()->sync(json_decode($this->request->categories, true));
                $this->audio->audioParents()->sync(json_decode($this->request->audioParents, true));
            });
        } catch (\Exception $e) {
            return response()->json([
                'updated' => false,
                'error' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'updated' => true,
            'data' => [
                'audio' => Audio::where('id', $this->audio->id)->first(),
                'audioParent' => $this->audio->audioParents,
                'category' => $this->audio->categories
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Audio  $audio
     * @return \Illuminate\Http\Response
     */
    public function destroy(Audio $audio)
    {
        try {
            $audio->delete();
            return response()->json([
                'deleted' => true,
                'data' => [
                    'audio' => $audio
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
