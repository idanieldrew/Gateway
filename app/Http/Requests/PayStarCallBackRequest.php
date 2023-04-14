<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayStarCallBackRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'status' => 'numeric',
            'order_id' => 'string',
            'ref_num' => 'string',
            'transaction_id' => 'string',
            'card_number' => 'nullable|string',
            'tracking_code' => 'nullable|string'
        ];
    }
}
