<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class CoursFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'date' => 'required|before:tomorrow',
            'vfrom' => 'required|min:3|max:3',
            'vto' => 'required|min:3|max:3'
        ];
    }

    public function messages(){
        return [
            'date.required' => 'Дата - Обязательный параметр',
            'date.before' => 'Задана не верная дата',
            'vfrom.required' => 'Валюта отдаю - Обязательный параметр',
            'vfrom.min' => 'Валюта отдаю - Неверный формат',
            'vfrom.max' => 'Валюта отдаю - Неверный формат',
            'vto.required' => 'Валюта получаю - Обязательный параметр',
            'vto.min' => 'Валюта получаю - Неверный формат',
            'vto.max' => 'Валюта получаю - Неверный формат'
        ];
    }
}
