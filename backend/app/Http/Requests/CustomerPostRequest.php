<?php

namespace App\Http\Requests;

use App\Http\Response\ResponseJson;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class CustomerPostRequest extends FormRequest
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
            'customer_name' => 'required|min:5|unique:customer,email',
            'email' => 'required|email',
            'tel_num' => 'required|min:10|numeric',
            'address' => 'required',
            'is_active' => 'required'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required' => trans('messages.validate.required'),
            'numeric' => trans('messages.validate.numeric'),
            'min' => trans('messages.validate.min'),
            'unique' => trans('messages.email.exist')
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'customer_name' => trans('messages.attributes.customer_name'),
            'email' => trans('messages.attributes.email'),
            'tel_num' => trans('messages.attributes.tel_num'),
            'address' => trans('messages.attributes.address'),
            'is_active' => trans('messages.attributes.is_active'),
        ];
    }

    public function validate($data){
        return Validator::make($data, $this->rules(), $this->messages(), $this->attributes());
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(
            ResponseJson::error($errors)
        );
    }
}
