<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class VersionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (request()->isMethod('GET')) {
            return [
                "limit" => "numeric",
                "page" => "numeric"
            ];
        }
        $method = request()->isMethod('POST') ? 'POST' : 'PATCH';
        return [
            "name" => $method === 'POST' ? "required|string" : "string",
            "playstore_url" => $method === 'POST' ? "required|string" : "string",
            "remark" => "string",
        ];
    }
}
