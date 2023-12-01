<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
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
            'mailadd' => [
                'required',
                'email:filter,dns',
                'max:255',
                Rule::unique('users', 'mailadd')->ignore($this->userId),
            ],
        ];
    }

    public function messages()
    {
        return [
            'mailadd.required' => 'メールアドレスを入力してください。',
            'mailadd.email' => 'メールアドレスを正しく入力してください。',
            'mailadd.max' => 'メールアドレスは255文字以内で入力してください。',
            'mailadd.unique' => '指定されたメールアドレスはすでに登録されています。',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400)
        );
    }
}
