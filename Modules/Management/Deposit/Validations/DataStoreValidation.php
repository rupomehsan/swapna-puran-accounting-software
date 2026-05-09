<?php

namespace Modules\Management\Deposit\Validations;

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
            'user_id'        => ['required', 'integer', 'exists:users,id'],
            'deposit_type'   => ['required', Rule::in(['share_deposit', 'extra_savings'])],
            'amount'         => ['required', 'numeric', 'min:1'],
            'for_month'      => ['required', 'date'],
            'payment_date'   => ['required', 'date'],
            'payment_method' => ['required', Rule::in(['cash', 'bank', 'mobile_banking'])],
            'due_id'         => ['sometimes', 'nullable', 'integer', 'exists:dues,id'],
            'note'           => ['nullable', 'string'],
            'image'          => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'received_by'    => ['nullable', 'integer', 'exists:users,id'],
            'status'         => ['sometimes', Rule::in(['active', 'inactive'])],
        ];
    }
}