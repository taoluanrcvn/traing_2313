<?php

namespace App\Http\Requests;

use App\Http\Response\ResponseJson;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
            'product_name' => 'required|min:5|max:255',
            'product_price' => 'required|min:1|numeric',
            'is_sales' => 'required',
            'inventory' => 'required|min:0|numeric',
            'product_image' => 'required',
            'description' => 'nullable'
        ];
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
            'max' => trans('messages.validate.max'),
            'mimes' => trans('messages.validate.mimes'),
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
            'product_name' => trans('messages.attributes.product_name'),
            'product_price' => trans('messages.attributes.product_price'),
            'is_sales' => trans('messages.attributes.is_sales'),
            'inventory' => trans('messages.attributes.inventory'),
            'product_image' => trans('messages.attributes.product_image'),
            'description'=> trans('messages.attributes.description'),
        ];
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
