<?php

namespace App\Http\Controllers;

use App\Models\Category_Audio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Category_Audio_Controller extends Controller
{
    public function category_audios(Request $request, $categoryId)
    {
        $this->request = $request;
        $this->categoryId = $categoryId;
        try {
            DB::transaction(function () {
                Category_Audio::where('category_id', $this->categoryId)->delete();
                foreach ($this->request->audioIds as $audioId) {
                    $category_audio = new Category_Audio();
                    $category_audio->category_id = $this->categoryId;
                    $category_audio->audio_id = $audioId;
                    $category_audio->save();
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


    public function audio_categories(Request $request, $audioId)
    {
        $this->request = $request;
        $this->audioId = $audioId;
        try {
            DB::transaction(function () {
                Category_Audio::where('audio_id', $this->audioId)->delete();
                foreach ($this->request->categoryIds as $categoryId) {
                    $category_audio = new Category_Audio();
                    $category_audio->category_id = $categoryId;
                    $category_audio->audio_id = $this->audioId;
                    $category_audio->save();
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
