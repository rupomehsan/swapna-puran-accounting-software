<?php

namespace Modules\Management\Account\Validations;

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

    public function validateError($data)
    {
        return response(['status' => 'validation_error', 'errors' => $data->getMessages()], 422);
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->validateError($validator->errors()));
    }

    public function rules(): array
    {
        $slug = $this->route('slug');
        $accountId = \Modules\Management\Account\Database\Models\Model::where('slug', $slug)->value('id');

        return [
            'account_code'    => ['sometimes', Rule::unique('accounts', 'account_code')->ignore($accountId)],
            'account_name'    => 'sometimes',
            'account_type'    => 'sometimes',
            'parent_id'       => 'sometimes|nullable',
            'opening_balance' => 'sometimes|nullable|numeric',
            'description'     => 'sometimes|nullable|string',
            'status'          => ['sometimes', Rule::in(['active', 'inactive'])],
        ];
    }
}
