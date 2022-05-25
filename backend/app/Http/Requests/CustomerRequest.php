<?php

namespace App\Http\Requests;

use App\Http\Response\ResponseJson;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
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
     * Các rules dùng chung.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_name' => 'required|min:5',
            'tel_num' => 'required|min:10|numeric',
            'address' => 'required',
            'is_active' => 'required'
        ] + ($this->isMethod('POST') ? $this->store() : $this->update());
    }

    /**
     * Set thông báo cho mỗi field khi lỗi.
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
     * custom tên lại cho attributes.
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


    protected function store()
    {
        return ['email' => 'required|email|unique:mst_customer,email'];
    }

    protected function update() {
        return ['email' => Rule::unique('mst_customer', 'email')->ignore($this->customer_id, 'customer_id')];
    }



    /**
     * trả về đúng format khi các trường không hợp lệ.
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
