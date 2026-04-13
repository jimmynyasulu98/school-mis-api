<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\PaymentResource;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentFeeAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/payments",
     *     tags={"Payments"},
     *     summary="List all payments with pagination and optional included relationships",
     *     description="Retrieve a paginated list of payments. Use 'includes' parameter to eager load related resources (account, account.student, account.feeStructure, recorder).",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1, minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of records per page (default 10, max 100)",
     *         required=false,
     *         @OA\Schema(type="integer", default=10, maximum=100, minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="includes",
     *         in="query",
     *         description="Comma-separated list of relationships to include. Available: account, account.student, account.feeStructure, recorder",
     *         example="account,account.student",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment collection retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PaginatedPaymentResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Insufficient permissions",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        return PaymentResource::collection(
            $this->applyPaginationAndIncludes(
                Payment::query(),
                $request,
                10
            )
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/payments",
     *     tags={"Payments"},
     *     summary="Record a payment and update fee balance",
     *     description="Create a new payment record for a student fee account",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Payment data to record",
     *         @OA\JsonContent(ref="#/components/schemas/PaymentStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payment recorded successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PaymentResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
     *     )
     * )
     */
    public function store(StorePaymentRequest $request)
    {
        $payload = $request->validated();

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

        return (new PaymentResource($payment->load('account.student', 'recorder')))
            ->response()
            ->setStatusCode(201);
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
