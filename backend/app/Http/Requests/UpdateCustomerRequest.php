<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Normalize phone
        if ($this->has('phone')) {
            $phone = $this->input('phone');
            $phone = preg_replace('/\s+/', '', $phone);
            $phone = ltrim($phone, '0');
            if (!str_starts_with($phone, '+')) {
                $phone = '0' . $phone;
            }
            $this->merge(['phone' => $phone]);
        }

        // Normalize name
        if ($this->has('name')) {
            $this->merge(['name' => trim($this->input('name'))]);
        }

        // Normalize address
        if ($this->has('address')) {
            $address = trim($this->input('address'));
            $this->merge(['address' => $address === '' ? null : $address]);
        }

        // Guard segment
        if ($this->has('segment')) {
            $segment = $this->input('segment');
            if (!in_array($segment, ['جملة', 'قطاعي', 'صفحة'])) {
                $this->merge(['segment' => 'قطاعي']);
            }
        }
    }

    public function rules(): array
    {
        $customerId = $this->route('id');
        
        return [
            'name' => ['sometimes', 'required', 'string', 'min:2', 'max:100'],
            'phone' => ['sometimes', 'required', 'string', 'min:10', 'max:20', Rule::unique('customers')->ignore($customerId)],
            'address' => ['nullable', 'string', 'max:255'],
            'segment' => ['sometimes', 'required', Rule::in(['جملة', 'قطاعي', 'صفحة'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'name.min' => 'الاسم يجب أن يكون على الأقل حرفين',
            'name.max' => 'الاسم يجب ألا يتجاوز 100 حرف',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.unique' => 'رقم الهاتف مسجل مسبقاً',
            'phone.min' => 'رقم الهاتف غير صحيح',
            'address.max' => 'العنوان طويل جداً',
            'segment.required' => 'الشريحة مطلوبة',
            'segment.in' => 'الشريحة غير صحيحة',
        ];
    }
}
