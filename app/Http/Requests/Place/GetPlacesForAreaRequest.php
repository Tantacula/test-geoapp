<?php

namespace App\Http\Requests\Place;

use App\Rules\GeometryPointRule;
use Illuminate\Foundation\Http\FormRequest;

class GetPlacesForAreaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'pointSW' => [
                'required',
                'json',
                new GeometryPointRule
            ],
            'pointNE' => [
                'required',
                'json',
                new GeometryPointRule
            ],
        ];
    }
}
