<?php

namespace App\Enums;

enum RepaymentState: string
{
  case PENDING = "PENDING";
  case PAID = "PAID";
}