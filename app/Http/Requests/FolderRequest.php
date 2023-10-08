<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FolderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'file' => 'required|array|max_uploaded_file_size:65536|user_limit_size',
            'file.*' => ['required', 'mimes:doc,rar,zip,7z,docx,pdf,jpeg,jpg,png,mp4,mp3',
                    Rule::unique('files','full_name')->where('folder_id',$this->input('id'))
                ],
            'id' => 'required'
        ];
    }
}
