<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Normalize phone: remove spaces, leading zeros
        if ($this->has('phone')) {
            $phone = $this->input('phone');
            $phone = preg_replace('/\s+/', '', $phone); // Remove spaces
            $phone = ltrim($phone, '0'); // Remove leading zeros
            if (!str_starts_with($phone, '+')) {
                $phone = '0' . $phone; // Add back single leading zero for Egyptian numbers
            }
            $this->merge(['phone' => $phone]);
        }

        // Normalize name: trim
        if ($this->has('name')) {
            $this->merge(['name' => trim($this->input('name'))]);
        }

        // Normalize address: trim or null
        if ($this->has('address')) {
            $address = trim($this->input('address'));
            $this->merge(['address' => $address === '' ? null : $address]);
        }

        // Guard segment: fallback to قطاعي
        if ($this->has('segment')) {
            $segment = $this->input('segment');
            if (!in_array($segment, ['جملة', 'قطاعي', 'صفحة'])) {
                $this->merge(['segment' => 'قطاعي']);
            }
        } else {
            $this->merge(['segment' => 'قطاعي']);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'phone' => ['required', 'string', 'min:10', 'max:20', 'unique:customers,phone'],
            'address' => ['nullable', 'string', 'max:255'],
            'segment' => ['required', Rule::in(['جملة', 'قطاعي', 'صفحة'])],
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
