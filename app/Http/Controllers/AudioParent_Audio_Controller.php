<?php

namespace App\Http\Controllers;

use App\Models\AudioParent_Audio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AudioParent_Audio_Controller extends Controller
{
    public function audioParent_audios(Request $request, $audioParentId)
    {
        $this->request = $request;
        $this->audioParentId = $audioParentId;
        try {
            DB::transaction(function () {
                AudioParent_Audio::where('audio_parent_id', $this->audioParentId)->delete();
                foreach ($this->request->audioIds as $audioId) {
                    $audioParent_audio = new AudioParent_Audio();
                    $audioParent_audio->audio_parent_id = $this->audioParentId;
                    $audioParent_audio->audio_id = $audioId;
                    $audioParent_audio->save();
                }
            });

            return response()->json([
                'status' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }


    public function audio_audioParents(Request $request, $audioId)
    {
        $this->request = $request;
        $this->audioId = $audioId;
        try {
            DB::transaction(function () {
                AudioParent_Audio::where('audio_id', $this->audioId)->delete();
                foreach ($this->request->audioParentIds as $audioParentId) {
                    $audioParent_audio = new AudioParent_Audio();
                    $audioParent_audio->audio_parent_id = $audioParentId;
                    $audioParent_audio->audio_id = $this->audioId;
                    $audioParent_audio->save();
                }
            });

            return response()->json([
                'status' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
