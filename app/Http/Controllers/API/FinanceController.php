<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;

class FinanceController extends Controller
{
    public function studentFees(Student $student)
    {
        return $student->load('feeAccounts.payments');
    }

    public function reportCard(Student $student)
    {
        return $student->load('grades');
    }

    public function collections()
    {
        return Payment::query()
            ->selectRaw('payment_date, payment_method, sum(amount_paid) as total_collected, count(*) as payments_count')
            ->groupBy('payment_date', 'payment_method')
            ->orderByDesc('payment_date')
            ->get();
    }
}
