<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RepaymentService;
use App\Http\Requests\SubmitRepaymentRequest;

class RepaymentController extends Controller
{
  protected $repaymentService;

  public function __construct(RepaymentService $repaymentService)
  {
    $this->repaymentService = $repaymentService;
  }

  public function store(SubmitRepaymentRequest $request, $loanId)
  {
    $data = $request->input('repayment');
    $repayment = $this->repaymentService->submitRepayment($data, $loanId);

    return response()->json(['repayment' => $repayment], 201);
  }
}