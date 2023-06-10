<?php

namespace App\Enums;

enum LoanState: string
{
  case PENDING = "PENDING";
  case APPROVED = "APPROVED";
  case PAID = "PAID";
}