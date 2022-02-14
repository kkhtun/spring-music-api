<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
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
                'certificates' => Certificate::orderBy('id', 'desc')->limit($limit)->offset(($page - 1) * $limit)->get(),
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
                'title' => 'required|max:1000',
                'certificateImg' => 'required|file'
            ]);
            $certificateImgFilePath = $request->file('certificateImg')->store('certificateImgs', 'public');

            $certificate = new Certificate();
            $certificate->title = $request->title;
            $certificate->certificate_img_file_path = $certificateImgFilePath;
            $certificate->save();

            return response()->json([
                'created' => true,
                'data' => [
                    'certificate' => $certificate
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
     * @param  \App\Models\Certificate  $certificate
     * @return \Illuminate\Http\Response
     */
    public function show(Certificate $certificate)
    {
        return response()->json([
            'status' => true,
            'data' => [
                'certificate' => $certificate,
            ]
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Certificate  $certificate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Certificate $certificate)
    {
        try {
            $certificateImgFilePath = is_null($request->certificateImg) ?  $certificate->certificate_img_file_path : $request->file('certificateImg')->store('certificateImgs', 'public');
            $certificate->title = is_null($request->title) ? $certificate->title : $request->title;
            $certificate->certificate_img_file_path = $certificateImgFilePath;
            $certificate->save();

            return response()->json([
                'updated' => true,
                'data' => [
                    'certificate' => $certificate
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
     * @param  \App\Models\Certificate  $certificate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Certificate $certificate)
    {
        try {
            $certificate->delete();
            return response()->json([
                'deleted' => true,
                'data' => [
                    'certificate' => $certificate
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
