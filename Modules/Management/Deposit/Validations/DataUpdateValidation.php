<?php

namespace Modules\Management\Deposit\Validations;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class DataUpdateValidation extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response(['status' => 'validation_error', 'errors' => $validator->errors()->getMessages()], 422)
        );
    }

    public function rules(): array
    {
        return [
            'user_id'        => ['sometimes', 'integer', 'exists:users,id'],
            'deposit_type'   => ['sometimes', Rule::in(['share_deposit', 'extra_savings'])],
            'amount'         => ['sometimes', 'numeric', 'min:1'],
            'for_month'      => ['sometimes', 'date'],
            'payment_date'   => ['sometimes', 'date'],
            'payment_method' => ['sometimes', Rule::in(['cash', 'bank', 'mobile_banking'])],
            'due_id'         => ['nullable', 'integer', 'exists:dues,id'],
            'note'           => ['nullable', 'string'],
            'received_by'    => ['nullable', 'integer', 'exists:users,id'],
            'status'         => ['sometimes', Rule::in(['active', 'inactive'])],
        ];
    }
}
