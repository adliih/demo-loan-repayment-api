<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitLoanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'loan.currency' => ['required', 'string'],
            'loan.amount' => ['required', 'numeric'],
            'loan.term' => ['required', 'numeric'],
            'loan.submitted_at' => ['required', 'date'],
        ];
    }
}