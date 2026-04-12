<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentFeeAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/payments",
     *     tags={"Payments"},
     *     summary="List payments",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Payment collection",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PaymentResource"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(
            Payment::with(['account.student', 'account.feeStructure.term'])->latest('payment_date')->paginate(20)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/payments",
     *     tags={"Payments"},
     *     summary="Record a payment and update fee balance",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PaymentStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payment saved",
     *         @OA\JsonContent(ref="#/components/schemas/PaymentResource")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            'student_fee_account_id' => ['required', 'uuid', 'exists:student_fee_accounts,id'],
            'amount_paid' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'receipt_number' => ['required', 'string', 'max:255', 'unique:payments,receipt_number'],
        ]);

        $payment = DB::transaction(function () use ($payload, $request) {
            $account = StudentFeeAccount::lockForUpdate()->findOrFail($payload['student_fee_account_id']);
            $account->update([
                'balance' => max(0, (float) $account->balance - (float) $payload['amount_paid']),
            ]);

            return Payment::create([
                ...$payload,
                'recorded_by' => $request->user()?->staff_id,
            ]);
        });

        return response()->json($payment->load('account.student'), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/students/{student}/fees",
     *     tags={"Payments"},
     *     summary="Get a student's fee accounts and payments",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="student", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(
     *         response=200,
     *         description="Student fee accounts",
     *         @OA\JsonContent(ref="#/components/schemas/StudentFeesResponse")
     *     )
     * )
     */
    public function studentFees(Student $student)
    {
        return response()->json($student->load('feeAccounts.feeStructure.term', 'feeAccounts.payments'));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/finance/reports/collections",
     *     tags={"Reports"},
     *     summary="Get daily collections grouped by payment method",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Collections report",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/CollectionsReportItem")
     *         )
     *     )
     * )
     */
    public function collections()
    {
        return Payment::query()
            ->selectRaw('payment_date, payment_method, sum(amount_paid) as total_collected, count(*) as payments_count')
            ->groupBy('payment_date', 'payment_method')
            ->orderByDesc('payment_date')
            ->get();
    }
}
