<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
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

        $query = Category::orderBy('id', 'desc');

        if ($minified) {
            $categories = $query->get(['category_name', 'id']);
            $count = $query->count();
        } else {
            $categories = $query->limit($limit)->offset(($page - 1) * $limit)->get();
            $count = $query->count();
        }

        return response()->json([
            'status' => true,
            'data' => [
                'categories' => $categories,
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
                'categoryName' => 'required|max:255',
                'iconCode' => 'nullable|max:255',
                'categoryArtwork' => 'nullable|file'
            ]);

            if ($request->categoryArtwork) {
                $categoryArtworkUrl = $request->file('categoryArtwork')->store('categoryArtwork', 's3');
            } else {
                $categoryArtworkUrl = null;
            }

            $category = new Category();
            $category->category_name = $request->categoryName;
            $category->icon_code = $request->iconCode;
            $category->category_artwork = $categoryArtworkUrl;
            $category->save();

            return response()->json([
                'created' => true,
                'data' => [
                    'category' => $category
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
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return response()->json([
            'status' => true,
            'data' => [
                'category' => $category,
                'audios' => $category->audios
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        try {

            if ($request->categoryArtwork) {
                $oldArtwork = $category->category_artwork;
                $category->category_artwork = $request->file('categoryArtwork')->store('categoryArtwork', 's3');
                Storage::disk("s3")->delete($oldArtwork);
            } else {
                $category->category_artwork = $category->category_artwork;
            }

            $category->category_name = is_null($request->categoryName) ? $category->category_name : $request->categoryName;
            $category->icon_code = is_null($request->iconCode) ? $category->icon_code : $request->iconCode;
            $category->save();

            return response()->json([
                'updated' => true,
                'data' => [
                    'category' => $category
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'updated' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {

        try {
            $category->delete();
            if ($category->category_artwork) {
                Storage::disk("s3")->delete($category->category_artwork);
            }
            return response()->json([
                'deleted' => true,
                'data' => [
                    'category' => $category
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
