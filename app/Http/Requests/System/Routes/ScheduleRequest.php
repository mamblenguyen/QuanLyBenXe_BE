<?php

namespace App\Http\Requests\System\Routes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ScheduleRequest extends FormRequest
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
        return [
            // 'route_id' => 'required',
            'departure_time' => 'required|date_format:H:i', // Departure time format
            'arrival_time' => 'required|date_format:H:i', // Arrival time format
            'ticket_price' => 'required|numeric|min:0', // Ticket price must be a positive number
          
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
            // 'route_id.required' => 'Mã tuyến xe không được để trống',
            'departure_time.required' => 'Thời gian khởi hành là bắt buộc.',
            'departure_time.date_format' => 'Thời gian khởi hành không đúng định dạng.',
            'arrival_time.required' => 'Thời gian đến là bắt buộc.',
            'arrival_time.date_format' => 'Thời gian đến không đúng định dạng.',
            'ticket_price.required' => 'Giá vé là bắt buộc.',
            'ticket_price.numeric' => 'Giá vé phải là một số.',
            'ticket_price.min' => 'Giá vé phải lớn hơn hoặc bằng 0.',
        
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
            'message' => 'Validation error occurred.',
            'errors' => $errors
        ], 422));
    }
}
