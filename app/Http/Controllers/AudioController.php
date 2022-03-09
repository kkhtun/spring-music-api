<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Models\AudioParent_Audio;
use App\Models\Category;
use App\Models\Category_Audio;
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

        $category_id = $request->get('category');
        $audio_parent_id = $request->get('audio_parent');
        $query = new Audio();
        if ($category_id) {
            $query = $query->whereHas('categories', function ($query) use ($category_id) {
                $query->where('category_id', $category_id);
            });
        }

        if ($audio_parent_id) {
            $query = $query->whereHas('audioParents', function ($query) use ($audio_parent_id) {
                $query->where('audio_parent_id', $audio_parent_id);
            });
        }

        $audios = $query->orderBy('id', 'desc')->limit($limit)->offset(($page - 1) * $limit)->get();
        $count = $query->count();

        $results = [];
        foreach ($audios as $audio) {
            array_push($results, [
                "audio" => $audio,
                "audioParents" => $audio->audioParents,
                "categories" => $audio->categories,
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'audios' => $results
            ],
            'count' => $count
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

            if ($request->artWorkFile) {
                $this->artWorkFilePath = $this->request->file('artWorkFile')->store('artWorkFiles', 's3');
            } else {
                $this->artWorkFilePath = null;
            }
            $this->audioFilePath = $this->request->file('audioFile')->store('audioFiles', 's3');
            $this->categories = $this->request->categories;
            $this->audioParents = $this->request->audioParents;

            DB::transaction(function () {
                $audio = new Audio();
                $audio->title = $this->request->title;
                $audio->art_work_file_path = $this->artWorkFilePath;
                $audio->audio_file_path = $this->audioFilePath;
                $audio->save();
                $audio->categories()->sync($this->request->categories);
                $audio->audioParents()->sync($this->request->audioParents);
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
        if ($request->artWorkFile) {
            $this->artWorkFilePath = $this->request->file('artWorkFile')->store('artWorkFiles', 's3');
            Storage::disk('s3')->delete($audio->art_work_file_path);
        } else {
            $this->artWorkFilePath = $audio->art_work_file_path;
        }
        if ($request->audioFile) {
            $this->audioFilePath = $this->request->file('audioFile')->store('audioFiles', 's3');
            Storage::disk('s3')->delete($audio->audio_file_path);
        } else {
            $this->audioFilePath = $audio->audio_file_path;
        }
        $this->data = [
            'title' => is_null($this->request->title) ? $this->audio->title : $this->request->title,
            'art_work_file_path' => $this->artWorkFilePath,
            'audio_file_path' => $this->audioFilePath,
        ];
        try {
            DB::transaction(function () {
                Audio::where('id', $this->audio->id)->update($this->data);
                $this->audio->categories()->sync($this->request->categories);
                $this->audio->audioParents()->sync($this->request->audioParents);
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
            Storage::disk('s3')->delete($audio->art_work_file_path);
            Storage::disk('s3')->delete($audio->audio_file_path);
            $audio->categories()->sync([]);
            $audio->audioParents()->sync([]);
            $audio->delete();
            return response()->json([
                'deleted' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'deleted' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
