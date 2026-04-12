<?php

namespace App\OpenApi;

class OpenApiComponents
{
    /**
 * @OA\Schema(
 *     schema="OpenApiComponentsContainer",
 *     type="object"
 * )
 *
 * @OA\Schema(
 *     schema="MessageResponse",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Operation completed successfully.")
 * )
 *
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         additionalProperties=@OA\Schema(
 *             type="array",
 *             @OA\Items(type="string")
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="UnauthorizedResponse",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Unauthenticated.")
 * )
 *
 * @OA\Schema(
 *     schema="GuardianLink",
 *     type="object",
 *     required={"id"},
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="is_primary", type="boolean", example=true),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Mother")
 * )
 *
 * @OA\Schema(
 *     schema="GuardianResource",
 *     type="object",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="first_name", type="string", example="Mary"),
 *     @OA\Property(property="last_name", type="string", example="Banda"),
 *     @OA\Property(property="phone", type="string", nullable=true, example="+265991234567"),
 *     @OA\Property(property="relationship", type="string", nullable=true, example="Mother"),
 *     @OA\Property(property="is_primary", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *     schema="ClassRoomSummary",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Form 1"),
 *     @OA\Property(property="stream", type="string", nullable=true, example="A")
 * )
 *
 * @OA\Schema(
 *     schema="StudentResource",
 *     type="object",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="admission_number", type="string", example="ADM-1001"),
 *     @OA\Property(property="first_name", type="string", example="Alice"),
 *     @OA\Property(property="last_name", type="string", example="Banda"),
 *     @OA\Property(property="gender", type="string", nullable=true, example="FEMALE"),
 *     @OA\Property(property="date_of_birth", type="string", format="date", nullable=true),
 *     @OA\Property(property="enrollment_date", type="string", format="date"),
 *     @OA\Property(property="status", type="string", example="ACTIVE"),
 *     @OA\Property(property="current_class", ref="#/components/schemas/ClassRoomSummary"),
 *     @OA\Property(
 *         property="guardians",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/GuardianResource")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="StudentSummary",
 *     type="object",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="admission_number", type="string", example="ADM-1001"),
 *     @OA\Property(property="first_name", type="string", example="Alice"),
 *     @OA\Property(property="last_name", type="string", example="Banda")
 * )
 *
 * @OA\Schema(
 *     schema="StudentStoreRequest",
 *     type="object",
 *     required={"admission_number","first_name","last_name","enrollment_date"},
 *     @OA\Property(property="admission_number", type="string", maxLength=50, example="ADM-1001"),
 *     @OA\Property(property="first_name", type="string", maxLength=100, example="Alice"),
 *     @OA\Property(property="last_name", type="string", maxLength=100, example="Banda"),
 *     @OA\Property(property="gender", type="string", nullable=true, example="FEMALE"),
 *     @OA\Property(property="date_of_birth", type="string", format="date", nullable=true),
 *     @OA\Property(property="enrollment_date", type="string", format="date", example="2026-01-10"),
 *     @OA\Property(property="status", type="string", nullable=true, example="ACTIVE"),
 *     @OA\Property(property="current_class_room_id", type="integer", nullable=true, example=1),
 *     @OA\Property(
 *         property="guardians",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/GuardianLink")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="StudentUpdateRequest",
 *     type="object",
 *     @OA\Property(property="first_name", type="string", maxLength=100, example="Alice"),
 *     @OA\Property(property="last_name", type="string", maxLength=100, example="Banda"),
 *     @OA\Property(property="gender", type="string", nullable=true, example="FEMALE"),
 *     @OA\Property(property="date_of_birth", type="string", format="date", nullable=true),
 *     @OA\Property(property="status", type="string", example="ACTIVE"),
 *     @OA\Property(property="current_class_room_id", type="integer", nullable=true, example=1)
 * )
 *
 * @OA\Schema(
 *     schema="StaffResource",
 *     type="object",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="employee_number", type="string", example="EMP-1001"),
 *     @OA\Property(property="first_name", type="string", example="John"),
 *     @OA\Property(property="last_name", type="string", example="Phiri"),
 *     @OA\Property(property="email", type="string", format="email", nullable=true),
 *     @OA\Property(property="phone", type="string", nullable=true),
 *     @OA\Property(property="job_title", type="string", nullable=true, example="Teacher"),
 *     @OA\Property(property="status", type="string", example="ACTIVE"),
 *     @OA\Property(property="roles", type="array", @OA\Items(type="string", example="teacher")),
 *     @OA\Property(property="created_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="StaffStoreRequest",
 *     type="object",
 *     required={"employee_number","first_name","last_name"},
 *     @OA\Property(property="employee_number", type="string", maxLength=50, example="EMP-1001"),
 *     @OA\Property(property="first_name", type="string", maxLength=100, example="John"),
 *     @OA\Property(property="last_name", type="string", maxLength=100, example="Phiri"),
 *     @OA\Property(property="gender", type="string", nullable=true, example="MALE"),
 *     @OA\Property(property="phone", type="string", nullable=true, example="+265991234567"),
 *     @OA\Property(property="email", type="string", format="email", nullable=true),
 *     @OA\Property(property="job_title", type="string", nullable=true, example="Teacher"),
 *     @OA\Property(property="hire_date", type="string", format="date", nullable=true),
 *     @OA\Property(property="status", type="string", nullable=true, example="ACTIVE"),
 *     @OA\Property(property="username", type="string", nullable=true, example="jphiri"),
 *     @OA\Property(property="password", type="string", minLength=8, nullable=true, example="secret123"),
 *     @OA\Property(property="roles", type="array", @OA\Items(type="string", example="teacher"))
 * )
 *
 * @OA\Schema(
 *     schema="StaffUpdateRequest",
 *     type="object",
 *     @OA\Property(property="first_name", type="string", maxLength=100, example="John"),
 *     @OA\Property(property="last_name", type="string", maxLength=100, example="Phiri"),
 *     @OA\Property(property="phone", type="string", nullable=true, example="+265991234567"),
 *     @OA\Property(property="email", type="string", format="email", nullable=true),
 *     @OA\Property(property="job_title", type="string", nullable=true, example="Teacher"),
 *     @OA\Property(property="status", type="string", example="ACTIVE"),
 *     @OA\Property(property="roles", type="array", @OA\Items(type="string", example="teacher"))
 * )
 *
 * @OA\Schema(
 *     schema="UserResource",
 *     type="object",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="username", type="string", example="admin"),
 *     @OA\Property(property="staff_id", type="string", format="uuid", nullable=true),
 *     @OA\Property(property="guardian_id", type="string", format="uuid", nullable=true),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="last_login_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="roles", type="array", @OA\Items(type="object")),
 *     @OA\Property(property="staff", ref="#/components/schemas/StaffResource"),
 *     @OA\Property(property="guardian", type="object", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     required={"username","password"},
 *     @OA\Property(property="username", type="string", example="admin"),
 *     @OA\Property(property="password", type="string", example="password")
 * )
 *
 * @OA\Schema(
 *     schema="LoginResponse",
 *     type="object",
 *     @OA\Property(property="token", type="string", example="1|long-sanctum-token"),
 *     @OA\Property(property="user", ref="#/components/schemas/UserResource")
 * )
 *
 * @OA\Schema(
 *     schema="AcademicYearResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="2026"),
 *     @OA\Property(property="start_date", type="string", format="date"),
 *     @OA\Property(property="end_date", type="string", format="date"),
 *     @OA\Property(property="is_current", type="boolean", example=true),
 *     @OA\Property(
 *         property="terms",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/TermResource")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="AcademicYearStoreRequest",
 *     type="object",
 *     required={"name","start_date","end_date"},
 *     @OA\Property(property="name", type="string", maxLength=20, example="2026"),
 *     @OA\Property(property="start_date", type="string", format="date", example="2026-01-01"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2026-12-31"),
 *     @OA\Property(property="is_current", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *     schema="AcademicYearUpdateRequest",
 *     type="object",
 *     @OA\Property(property="name", type="string", maxLength=20, example="2026"),
 *     @OA\Property(property="start_date", type="string", format="date"),
 *     @OA\Property(property="end_date", type="string", format="date"),
 *     @OA\Property(property="is_current", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *     schema="TermResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="academic_year_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Term 1"),
 *     @OA\Property(property="start_date", type="string", format="date"),
 *     @OA\Property(property="end_date", type="string", format="date"),
 *     @OA\Property(property="is_current", type="boolean", example=true),
 *     @OA\Property(property="academic_year", ref="#/components/schemas/AcademicYearResource"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="TermStoreRequest",
 *     type="object",
 *     required={"academic_year_id","name","start_date","end_date"},
 *     @OA\Property(property="academic_year_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", maxLength=30, example="Term 1"),
 *     @OA\Property(property="start_date", type="string", format="date"),
 *     @OA\Property(property="end_date", type="string", format="date"),
 *     @OA\Property(property="is_current", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *     schema="TermUpdateRequest",
 *     type="object",
 *     @OA\Property(property="name", type="string", maxLength=30, example="Term 1"),
 *     @OA\Property(property="start_date", type="string", format="date"),
 *     @OA\Property(property="end_date", type="string", format="date"),
 *     @OA\Property(property="is_current", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *     schema="ClassRoomResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Form 1"),
 *     @OA\Property(property="stream", type="string", nullable=true, example="A"),
 *     @OA\Property(property="section", type="string", nullable=true, example="North Wing"),
 *     @OA\Property(property="class_teacher_id", type="string", format="uuid", nullable=true),
 *     @OA\Property(property="class_teacher", ref="#/components/schemas/StaffResource"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="ClassRoomStoreRequest",
 *     type="object",
 *     required={"name"},
 *     @OA\Property(property="name", type="string", maxLength=255, example="Form 1"),
 *     @OA\Property(property="stream", type="string", nullable=true, example="A"),
 *     @OA\Property(property="section", type="string", nullable=true, example="North Wing"),
 *     @OA\Property(property="class_teacher_id", type="string", format="uuid", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="ClassRoomUpdateRequest",
 *     type="object",
 *     @OA\Property(property="name", type="string", maxLength=255, example="Form 1"),
 *     @OA\Property(property="stream", type="string", nullable=true, example="A"),
 *     @OA\Property(property="section", type="string", nullable=true, example="North Wing"),
 *     @OA\Property(property="class_teacher_id", type="string", format="uuid", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="SubjectResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Mathematics"),
 *     @OA\Property(property="code", type="string", example="MATH"),
 *     @OA\Property(property="is_core", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="SubjectStoreRequest",
 *     type="object",
 *     required={"name","code"},
 *     @OA\Property(property="name", type="string", maxLength=255, example="Mathematics"),
 *     @OA\Property(property="code", type="string", maxLength=20, example="MATH"),
 *     @OA\Property(property="is_core", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *     schema="SubjectUpdateRequest",
 *     type="object",
 *     @OA\Property(property="name", type="string", maxLength=255, example="Mathematics"),
 *     @OA\Property(property="code", type="string", maxLength=20, example="MATH"),
 *     @OA\Property(property="is_core", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *     schema="AssessmentResource",
 *     type="object",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="class_subject_id", type="integer", example=1),
 *     @OA\Property(property="term_id", type="integer", example=1),
 *     @OA\Property(property="assessment_type_id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Midterm Exam"),
 *     @OA\Property(property="max_score", type="number", format="float", example=100),
 *     @OA\Property(property="assessment_date", type="string", format="date"),
 *     @OA\Property(property="grades", type="array", @OA\Items(ref="#/components/schemas/StudentGradeSummary")),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="AssessmentSummary",
 *     type="object",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="title", type="string", example="Midterm Exam"),
 *     @OA\Property(property="max_score", type="number", format="float", example=100),
 *     @OA\Property(property="assessment_date", type="string", format="date")
 * )
 *
 * @OA\Schema(
 *     schema="AssessmentStoreRequest",
 *     type="object",
 *     required={"class_subject_id","term_id","assessment_type_id","title","max_score","assessment_date"},
 *     @OA\Property(property="class_subject_id", type="integer", example=1),
 *     @OA\Property(property="term_id", type="integer", example=1),
 *     @OA\Property(property="assessment_type_id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", maxLength=255, example="Midterm Exam"),
 *     @OA\Property(property="max_score", type="number", format="float", example=100),
 *     @OA\Property(property="assessment_date", type="string", format="date")
 * )
 *
 * @OA\Schema(
 *     schema="AssessmentUpdateRequest",
 *     type="object",
 *     @OA\Property(property="title", type="string", maxLength=255, example="Midterm Exam"),
 *     @OA\Property(property="max_score", type="number", format="float", example=100),
 *     @OA\Property(property="assessment_date", type="string", format="date")
 * )
 *
 * @OA\Schema(
 *     schema="StudentGradeResource",
 *     type="object",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="student_id", type="string", format="uuid"),
 *     @OA\Property(property="assessment_id", type="string", format="uuid"),
 *     @OA\Property(property="score", type="number", format="float", example=78.5),
 *     @OA\Property(property="grade_letter", type="string", example="B"),
 *     @OA\Property(property="remarks", type="string", nullable=true, example="Improved performance"),
 *     @OA\Property(property="recorded_by", type="string", format="uuid", nullable=true),
 *     @OA\Property(property="recorded_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="student", ref="#/components/schemas/StudentSummary"),
 *     @OA\Property(property="assessment", ref="#/components/schemas/AssessmentSummary"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="StudentGradeSummary",
 *     type="object",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="student_id", type="string", format="uuid"),
 *     @OA\Property(property="assessment_id", type="string", format="uuid"),
 *     @OA\Property(property="score", type="number", format="float", example=78.5),
 *     @OA\Property(property="grade_letter", type="string", example="B"),
 *     @OA\Property(property="remarks", type="string", nullable=true, example="Improved performance"),
 *     @OA\Property(property="recorded_at", type="string", format="date-time", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="GradeStoreRequest",
 *     type="object",
 *     required={"student_id","assessment_id","score"},
 *     @OA\Property(property="student_id", type="string", format="uuid"),
 *     @OA\Property(property="assessment_id", type="string", format="uuid"),
 *     @OA\Property(property="score", type="number", format="float", example=78.5),
 *     @OA\Property(property="remarks", type="string", nullable=true, example="Improved performance")
 * )
 *
 * @OA\Schema(
 *     schema="AttendanceResource",
 *     type="object",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="student_id", type="string", format="uuid"),
 *     @OA\Property(property="class_room_id", type="integer", example=1),
 *     @OA\Property(property="attendance_date", type="string", format="date"),
 *     @OA\Property(property="status", type="string", enum={"PRESENT","ABSENT","LATE","EXCUSED"}),
 *     @OA\Property(property="recorded_by", type="string", format="uuid", nullable=true),
 *     @OA\Property(property="student", ref="#/components/schemas/StudentSummary"),
 *     @OA\Property(property="class_room", ref="#/components/schemas/ClassRoomSummary"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="AttendanceStoreRequest",
 *     type="object",
 *     required={"student_id","class_room_id","attendance_date","status"},
 *     @OA\Property(property="student_id", type="string", format="uuid"),
 *     @OA\Property(property="class_room_id", type="integer", example=1),
 *     @OA\Property(property="attendance_date", type="string", format="date"),
 *     @OA\Property(property="status", type="string", enum={"PRESENT","ABSENT","LATE","EXCUSED"})
 * )
 *
 * @OA\Schema(
 *     schema="FeeStructureSummary",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="term_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", nullable=true, example="Term 1 Fees")
 * )
 *
 * @OA\Schema(
 *     schema="StudentFeeAccountResource",
 *     type="object",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="student_id", type="string", format="uuid"),
 *     @OA\Property(property="fee_structure_id", type="integer", example=1),
 *     @OA\Property(property="balance", type="number", format="float", example=350000),
 *     @OA\Property(property="fee_structure", ref="#/components/schemas/FeeStructureSummary"),
 *     @OA\Property(property="payments", type="array", @OA\Items(ref="#/components/schemas/PaymentSummary"))
 * )
 *
 * @OA\Schema(
 *     schema="StudentFeeAccountSummary",
 *     type="object",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="student_id", type="string", format="uuid"),
 *     @OA\Property(property="fee_structure_id", type="integer", example=1),
 *     @OA\Property(property="balance", type="number", format="float", example=350000)
 * )
 *
 * @OA\Schema(
 *     schema="PaymentResource",
 *     type="object",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="student_fee_account_id", type="string", format="uuid"),
 *     @OA\Property(property="amount_paid", type="number", format="float", example=150000),
 *     @OA\Property(property="payment_date", type="string", format="date"),
 *     @OA\Property(property="payment_method", type="string", nullable=true, example="MPAMBA"),
 *     @OA\Property(property="receipt_number", type="string", example="RCT-1001"),
 *     @OA\Property(property="recorded_by", type="string", format="uuid", nullable=true),
 *     @OA\Property(property="account", ref="#/components/schemas/StudentFeeAccountSummary"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="PaymentSummary",
 *     type="object",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="amount_paid", type="number", format="float", example=150000),
 *     @OA\Property(property="payment_date", type="string", format="date"),
 *     @OA\Property(property="payment_method", type="string", nullable=true, example="MPAMBA"),
 *     @OA\Property(property="receipt_number", type="string", example="RCT-1001")
 * )
 *
 * @OA\Schema(
 *     schema="StudentFeesResponse",
 *     type="object",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="admission_number", type="string", example="ADM-1001"),
 *     @OA\Property(property="first_name", type="string", example="Alice"),
 *     @OA\Property(property="last_name", type="string", example="Banda"),
 *     @OA\Property(
 *         property="fee_accounts",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/StudentFeeAccountResource")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="PaymentStoreRequest",
 *     type="object",
 *     required={"student_fee_account_id","amount_paid","payment_date","receipt_number"},
 *     @OA\Property(property="student_fee_account_id", type="string", format="uuid"),
 *     @OA\Property(property="amount_paid", type="number", format="float", example=150000),
 *     @OA\Property(property="payment_date", type="string", format="date"),
 *     @OA\Property(property="payment_method", type="string", nullable=true, example="MPAMBA"),
 *     @OA\Property(property="receipt_number", type="string", example="RCT-1001")
 * )
 *
 * @OA\Schema(
 *     schema="RoleResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="teacher"),
 *     @OA\Property(property="guard_name", type="string", example="api"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Teaching staff role"),
 *     @OA\Property(property="permissions", type="array", @OA\Items(ref="#/components/schemas/PermissionResource"))
 * )
 *
 * @OA\Schema(
 *     schema="RoleStoreRequest",
 *     type="object",
 *     required={"name"},
 *     @OA\Property(property="name", type="string", example="teacher"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Teaching staff role"),
 *     @OA\Property(property="permission_ids", type="array", @OA\Items(type="integer", example=1))
 * )
 *
 * @OA\Schema(
 *     schema="PermissionResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="students.view"),
 *     @OA\Property(property="guard_name", type="string", example="api"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Allows viewing students")
 * )
 *
 * @OA\Schema(
 *     schema="PermissionStoreRequest",
 *     type="object",
 *     required={"name"},
 *     @OA\Property(property="name", type="string", example="students.view"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Allows viewing students")
 * )
 *
 * @OA\Schema(
 *     schema="CollectionsReportItem",
 *     type="object",
 *     @OA\Property(property="payment_date", type="string", format="date"),
 *     @OA\Property(property="payment_method", type="string", nullable=true, example="MPAMBA"),
 *     @OA\Property(property="total_collected", type="number", format="float", example=450000),
 *     @OA\Property(property="payments_count", type="integer", example=3)
 * )
 *
 * @OA\Schema(
 *     schema="ReportCardSubjectAssessment",
 *     type="object",
 *     @OA\Property(property="assessment", type="string", example="Midterm Exam"),
 *     @OA\Property(property="type", type="string", example="Exam"),
 *     @OA\Property(property="score", type="number", format="float", example=78.5),
 *     @OA\Property(property="max_score", type="number", format="float", example=100),
 *     @OA\Property(property="grade_letter", type="string", example="B"),
 *     @OA\Property(property="remarks", type="string", nullable=true, example="Improved performance")
 * )
 *
 * @OA\Schema(
 *     schema="ReportCardSubject",
 *     type="object",
 *     @OA\Property(property="subject", type="string", example="Mathematics"),
 *     @OA\Property(property="average_score", type="number", format="float", example=76.25),
 *     @OA\Property(property="grade_letter", type="string", example="B"),
 *     @OA\Property(property="assessments", type="array", @OA\Items(ref="#/components/schemas/ReportCardSubjectAssessment"))
 * )
 *
 * @OA\Schema(
 *     schema="ReportCardResponse",
 *     type="object",
 *     @OA\Property(
 *         property="student",
 *         type="object",
 *         @OA\Property(property="id", type="string", format="uuid"),
 *         @OA\Property(property="admission_number", type="string", example="ADM-1001"),
 *         @OA\Property(property="name", type="string", example="Alice Banda"),
 *         @OA\Property(property="class_room_id", type="integer", nullable=true, example=1)
 *     ),
 *     @OA\Property(
 *         property="summary",
 *         type="object",
 *         @OA\Property(property="subject_count", type="integer", example=8),
 *         @OA\Property(property="overall_average", type="number", format="float", example=74.2),
 *         @OA\Property(property="overall_grade", type="string", example="B"),
 *         @OA\Property(property="fee_balance", type="number", format="float", example=120000),
 *         @OA\Property(
 *             property="attendance",
 *             type="object",
 *             additionalProperties=@OA\Schema(type="integer", example=12)
 *         )
 *     ),
 *     @OA\Property(property="subjects", type="array", @OA\Items(ref="#/components/schemas/ReportCardSubject")),
 *     @OA\Property(property="grade_history", type="array", @OA\Items(type="object"))
 * )
     */
    public array $components = [];
}
