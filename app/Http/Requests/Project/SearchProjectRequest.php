<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class SearchProjectRequest extends FormRequest
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
        return [
            'keyword' => 'sometimes|string',
            'status' => 'sometimes|integer',
            'creator' => 'sometimes|integer',
            'created_before' => 'sometimes|date_format: Y-m-d',
            'create_after' => 'sometimes|date_format: Y-m-d'
        ];
    }
}
