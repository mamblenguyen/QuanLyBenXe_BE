<?php

namespace App\Http\Requests\System\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow the request
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('id'); // Lấy ID người dùng từ route

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId, // Cho phép email hiện tại
            'password' => 'nullable|string|min:8',
            'birthday' => 'required|nullable|date',
            'gender' => 'nullable|in:Nam,Nữ,Khác',
            'phone' => 'required|nullable|string|max:15',
            'address' => 'required|nullable|string',
            'avatar' => 'nullable', // Avatar có thể không bắt buộc khi cập nhật
            'identity_card' => 'required|string|unique:users,identity_card,' . $userId . '|max:20',
            'identity_issue_place' => 'required|nullable|string|max:255',
            'identity_issue_date' => 'required|nullable|date',
            'status_id' => 'required',
            'license' => 'nullable|string|max:10',
            'license_issue_date' => 'nullable|date',
            'license_expiry_date' => 'nullable|date|after_or_equal:license_issue_date',
            'roles' => 'nullable|array',
            // 'roles.*' => 'exists:roles,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên người dùng là bắt buộc.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã được sử dụng bởi người dùng khác.',
            'address.required' => 'Địa chỉ là bắt buộc.',
            'phone.required' => 'Số điện thoại là bắt buộc',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'birthday.date' => 'Ngày sinh phải là một ngày hợp lệ.',
            'birthday.required' => 'Ngày sinh là bắt buộc.',
            'gender.in' => 'Giới tính phải là Nam, Nữ, hoặc Khác.',
            'identity_card.required' => 'Số CMND/CCCD là bắt buộc.',
            'identity_card.unique' => 'Số CMND/CCCD đã được sử dụng.',
            'status_id.required' => 'Trạng thái là bắt buộc.',
            'identity_issue_place.required' => 'Nơi cấp CMND/CCCD là bắt buộc.',
            'identity_issue_date.required' => 'Ngày cấp CMND/CCCD là bắt buộc.',
            'identity_card.max' => 'Số CMND/CCCD không được vượt quá 20 ký tự.',
            'license.max' => 'Số giấy phép không được vượt quá 10 ký tự.',
            'license_expiry_date.after_or_equal' => 'Ngày hết hạn giấy phép phải sau ngày cấp phép.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Đã xảy ra lỗi khi gửi yêu cầu.',
            'errors' => $errors
        ], 422));
    }
}
