<?php

namespace Modules\Management\UserManagement\User\Validations;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class DataStoreValidation extends FormRequest
{
    /**
     * Determine if the  is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    /**
     * validateError to make this request.
     */
    public function validateError($data)
    {
        $errorPayload =  $data->getMessages();
        return response(['status' => 'validation_error', 'errors' => $errorPayload], 422);
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->validateError($validator->errors()));
        if ($this->wantsJson() || $this->ajax()) {
            throw new HttpResponseException($this->validateError($validator->errors()));
        }
        parent::failedValidation($validator);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'role_id' => 'required | sometimes',
            'name' => 'required|string|max:255',
            'phone' => 'required|unique:users',
            'email' => 'sometimes', 
            'password' => 'required|sometimes',
            'image' => 'nullable | sometimes',
            'number_of_share' => 'required',
            'join_date' => 'required',
            'nominee_name'     => 'nullable|sometimes|string|max:100',
            'nominee_relation' => 'nullable|sometimes|string|max:50',
            'nominee_nid'      => 'nullable|sometimes|string|max:50',
            'nominee_image'    => 'nullable|sometimes',
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
        ];
    }
}
