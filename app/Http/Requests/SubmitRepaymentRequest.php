<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitRepaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'repayment.currency' => 'required|string',
            'repayment.amount' => 'required|numeric',
            'repayment.scheduled_repayment_id' => 'required|exists:scheduled_repayments,id',
        ];
    }
}