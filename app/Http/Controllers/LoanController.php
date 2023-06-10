<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Services\LoanService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SubmitLoanRequest;

class LoanController extends Controller
{
  protected $loanService;

  public function __construct(LoanService $loanService)
  {
    $this->loanService = $loanService;
  }

  public function index()
  {
    $user = Auth::user();
    $loans = $this->loanService->getCustomerLoans($user->id);

    return response()->json(['loans' => $loans], 200);
  }

  public function store(SubmitLoanRequest $request)
  {
    $user = Auth::user();
    $data = $request->input('loan');

    $loan = $this->loanService->submitLoan($data, $user->id);

    return response()->json(['loan' => $loan], 201);
  }

  public function approve(int $loanId)
  {
    $loan = Loan::findOrFail($loanId);

    $loan = $this->loanService->approveLoan($loan);

    return response()->json(['loan' => $loan], 200);
  }
}