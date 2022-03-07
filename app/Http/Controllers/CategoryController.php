<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

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

        $query = Category::orderBy('id', 'desc');

        $category = $query->limit($limit)->offset(($page - 1) * $limit)->get();
        $count = $query->count();

        return response()->json([
            'status' => true,
            'data' => [
                'categories' => $category,
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
                'iconCode' => 'nullable|max:255'
            ]);

            $category = new Category();
            $category->category_name = $request->categoryName;
            $category->icon_code = $request->iconCode;
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
