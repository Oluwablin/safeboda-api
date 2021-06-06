<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePromoRequest extends FormRequest
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
            'id' => 'required|uuid',
            'code' => [
                'required',
                'string',
                Rule::unique('promos')->ignore($this->code, 'code'),
            ],
            'value' => 'required|numeric|gt:0',
            'venue' => 'required|string',
            'radius' => 'required|numeric|gt:0',
            'expiry_date' => 'required|date|after_or_equal:' . date('Y-m-d')
        ];
    }
}
