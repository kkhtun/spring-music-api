<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $query = Certificate::orderBy('id', 'desc');
        return response()->json([
            'status' => true,
            'data' => [
                'certificates' => $query->limit($limit)->offset(($page - 1) * $limit)->get(),
            ],
            'count' => $query->count()
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
                'title' => 'required|max:191',
                'certificateImg' => 'required|file'
            ]);
            $certificateImgFilePath = $request->file('certificateImg')->store('certificateImgs', 's3');
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
            if ($request->certificateImg) {
                $certificateImgFilePath = $request->file('certificateImg')->store('certificateImgs', 's3');
                Storage::disk('s3')->delete($certificate->certificate_img_file_path);
            } else {
                $certificateImgFilePath = $certificate->certificate_img_file_path;
            }
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
            if ($certificate->certificate_img_file_path) {
                Storage::disk('s3')->delete($certificate->certificate_img_file_path);
            }
            return response()->json([
                'deleted' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'deleted' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
