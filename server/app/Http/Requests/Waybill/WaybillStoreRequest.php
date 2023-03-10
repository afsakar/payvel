<?php

namespace App\Http\Requests\Waybill;

use Illuminate\Foundation\Http\FormRequest;

class WaybillStoreRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'number' => 'required|string|min:5|unique:waybills,number',
            'company_id' => 'required|integer',
            'corporation_id' => 'required|integer',
            'address' => 'required|string',
            'status' => 'required|string',
            'content' => 'required|string',
            'due_date' => 'nullable|date',
            'waybill_date' => 'nullable|date',
        ];
    }

    public function attributes()
    {
        return [
            'number' => 'Number',
            'company_id' => 'Company',
            'corporation_id' => 'Corporation',
            'address' => 'Address',
            'status' => 'Status',
            'content' => 'Content',
        ];
    }
}
