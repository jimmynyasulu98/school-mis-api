<?php

namespace App\OpenApi;

/**
 * @OA\Schema(schema="MessageResponse", type="object", @OA\Property(property="message", type="string", example="Operation completed successfully."))
 */
class MessageResponseSchema {}

/**
 * @OA\Schema(
 *   schema="ValidationErrorResponse",
 *   type="object",
 *   @OA\Property(property="message", type="string", example="The given data was invalid."),
 *   @OA\Property(property="errors", type="object", additionalProperties=@OA\Schema(type="array", @OA\Items(type="string")))
 * )
 */
class ValidationErrorResponseSchema {}

/**
 * @OA\Schema(schema="UnauthorizedResponse", type="object", @OA\Property(property="message", type="string", example="Unauthenticated."))
 */
class UnauthorizedResponseSchema {}

/**
 * @OA\Schema(
 *   schema="LoginRequest",
 *   type="object",
 *   required={"username","password"},
 *   @OA\Property(property="username", type="string", example="admin"),
 *   @OA\Property(property="password", type="string", example="password")
 * )
 */
class LoginRequestSchema {}

/**
 * @OA\Schema(
 *   schema="LoginResponse",
 *   type="object",
 *   @OA\Property(property="token", type="string", example="1|long-sanctum-token"),
 *   @OA\Property(property="user", ref="#/components/schemas/UserResource")
 * )
 */
class LoginResponseSchema {}

/**
 * @OA\Schema(
 *   schema="UserResource",
 *   type="object",
 *   @OA\Property(property="id", type="string", format="uuid"),
 *   @OA\Property(property="username", type="string", example="admin"),
 *   @OA\Property(property="staff_id", type="string", format="uuid", nullable=true),
 *   @OA\Property(property="guardian_id", type="string", format="uuid", nullable=true),
 *   @OA\Property(property="is_active", type="boolean", example=true),
 *   @OA\Property(property="last_login_at", type="string", format="date-time", nullable=true),
 *   @OA\Property(property="roles", type="array", @OA\Items(type="object")),
 *   @OA\Property(property="staff", type="object", nullable=true),
 *   @OA\Property(property="guardian", type="object", nullable=true)
 * )
 */
class UserResourceSchema {}

/**
 * @OA\Schema(
 *   schema="StudentStoreRequest",
 *   type="object",
 *   required={"admission_number","first_name","last_name","enrollment_date"},
 *   @OA\Property(property="admission_number", type="string", example="ADM-1001"),
 *   @OA\Property(property="first_name", type="string", example="Alice"),
 *   @OA\Property(property="last_name", type="string", example="Banda"),
 *   @OA\Property(property="gender", type="string", nullable=true, example="FEMALE"),
 *   @OA\Property(property="date_of_birth", type="string", format="date", nullable=true),
 *   @OA\Property(property="enrollment_date", type="string", format="date"),
 *   @OA\Property(property="status", type="string", nullable=true, example="ACTIVE"),
 *   @OA\Property(property="current_class_room_id", type="integer", nullable=true, example=1),
 *   @OA\Property(property="guardians", type="array", @OA\Items(type="object", @OA\Property(property="id", type="string", format="uuid"), @OA\Property(property="is_primary", type="boolean", example=true), @OA\Property(property="notes", type="string", nullable=true, example="Mother")))
 * )
 */
class StudentStoreRequestSchema {}

/**
 * @OA\Schema(
 *   schema="StudentUpdateRequest",
 *   type="object",
 *   @OA\Property(property="first_name", type="string", example="Alice"),
 *   @OA\Property(property="last_name", type="string", example="Banda"),
 *   @OA\Property(property="gender", type="string", nullable=true, example="FEMALE"),
 *   @OA\Property(property="date_of_birth", type="string", format="date", nullable=true),
 *   @OA\Property(property="status", type="string", example="ACTIVE"),
 *   @OA\Property(property="current_class_room_id", type="integer", nullable=true, example=1)
 * )
 */
class StudentUpdateRequestSchema {}

/**
 * @OA\Schema(
 *   schema="StudentResource",
 *   type="object",
 *   @OA\Property(property="id", type="string", format="uuid"),
 *   @OA\Property(property="admission_number", type="string", example="ADM-1001"),
 *   @OA\Property(property="first_name", type="string", example="Alice"),
 *   @OA\Property(property="last_name", type="string", example="Banda"),
 *   @OA\Property(property="gender", type="string", nullable=true, example="FEMALE"),
 *   @OA\Property(property="date_of_birth", type="string", format="date", nullable=true),
 *   @OA\Property(property="enrollment_date", type="string", format="date"),
 *   @OA\Property(property="status", type="string", example="ACTIVE"),
 *   @OA\Property(property="current_class", type="object"),
 *   @OA\Property(property="guardians", type="array", @OA\Items(type="object")),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class StudentResourceSchema {}

/**
 * @OA\Schema(
 *   schema="StaffStoreRequest",
 *   type="object",
 *   required={"employee_number","first_name","last_name"},
 *   @OA\Property(property="employee_number", type="string", example="EMP-1001"),
 *   @OA\Property(property="first_name", type="string", example="John"),
 *   @OA\Property(property="last_name", type="string", example="Phiri"),
 *   @OA\Property(property="gender", type="string", nullable=true),
 *   @OA\Property(property="phone", type="string", nullable=true),
 *   @OA\Property(property="email", type="string", format="email", nullable=true),
 *   @OA\Property(property="job_title", type="string", nullable=true),
 *   @OA\Property(property="hire_date", type="string", format="date", nullable=true),
 *   @OA\Property(property="status", type="string", nullable=true),
 *   @OA\Property(property="username", type="string", nullable=true),
 *   @OA\Property(property="password", type="string", nullable=true),
 *   @OA\Property(property="roles", type="array", @OA\Items(type="string"))
 * )
 */
class StaffStoreRequestSchema {}

/**
 * @OA\Schema(schema="StaffUpdateRequest", type="object", @OA\Property(property="first_name", type="string"), @OA\Property(property="last_name", type="string"), @OA\Property(property="phone", type="string", nullable=true), @OA\Property(property="email", type="string", format="email", nullable=true), @OA\Property(property="job_title", type="string", nullable=true), @OA\Property(property="status", type="string"), @OA\Property(property="roles", type="array", @OA\Items(type="string")))
 */
class StaffUpdateRequestSchema {}

/**
 * @OA\Schema(schema="StaffResource", type="object", @OA\Property(property="id", type="string", format="uuid"), @OA\Property(property="employee_number", type="string"), @OA\Property(property="first_name", type="string"), @OA\Property(property="last_name", type="string"), @OA\Property(property="email", type="string", format="email", nullable=true), @OA\Property(property="phone", type="string", nullable=true), @OA\Property(property="job_title", type="string", nullable=true), @OA\Property(property="status", type="string"), @OA\Property(property="roles", type="array", @OA\Items(type="string")), @OA\Property(property="created_at", type="string", format="date-time"))
 */
class StaffResourceSchema {}

/**
 * @OA\Schema(schema="AcademicYearStoreRequest", type="object", required={"name","start_date","end_date"}, @OA\Property(property="name", type="string"), @OA\Property(property="start_date", type="string", format="date"), @OA\Property(property="end_date", type="string", format="date"), @OA\Property(property="is_current", type="boolean"))
 */
class AcademicYearStoreRequestSchema {}

/**
 * @OA\Schema(schema="AcademicYearUpdateRequest", type="object", @OA\Property(property="name", type="string"), @OA\Property(property="start_date", type="string", format="date"), @OA\Property(property="end_date", type="string", format="date"), @OA\Property(property="is_current", type="boolean"))
 */
class AcademicYearUpdateRequestSchema {}

/**
 * @OA\Schema(schema="AcademicYearResource", type="object", @OA\Property(property="id", type="integer"), @OA\Property(property="name", type="string"), @OA\Property(property="start_date", type="string", format="date"), @OA\Property(property="end_date", type="string", format="date"), @OA\Property(property="is_current", type="boolean"), @OA\Property(property="terms", type="array", @OA\Items(type="object")), @OA\Property(property="created_at", type="string", format="date-time"), @OA\Property(property="updated_at", type="string", format="date-time"))
 */
class AcademicYearResourceSchema {}

/**
 * @OA\Schema(schema="TermStoreRequest", type="object", required={"academic_year_id","name","start_date","end_date"}, @OA\Property(property="academic_year_id", type="integer"), @OA\Property(property="name", type="string"), @OA\Property(property="start_date", type="string", format="date"), @OA\Property(property="end_date", type="string", format="date"), @OA\Property(property="is_current", type="boolean"))
 */
class TermStoreRequestSchema {}

/**
 * @OA\Schema(schema="TermUpdateRequest", type="object", @OA\Property(property="name", type="string"), @OA\Property(property="start_date", type="string", format="date"), @OA\Property(property="end_date", type="string", format="date"), @OA\Property(property="is_current", type="boolean"))
 */
class TermUpdateRequestSchema {}

/**
 * @OA\Schema(schema="TermResource", type="object", @OA\Property(property="id", type="integer"), @OA\Property(property="academic_year_id", type="integer"), @OA\Property(property="name", type="string"), @OA\Property(property="start_date", type="string", format="date"), @OA\Property(property="end_date", type="string", format="date"), @OA\Property(property="is_current", type="boolean"), @OA\Property(property="academic_year", type="object"), @OA\Property(property="created_at", type="string", format="date-time"), @OA\Property(property="updated_at", type="string", format="date-time"))
 */
class TermResourceSchema {}

/**
 * @OA\Schema(schema="ClassRoomStoreRequest", type="object", required={"name"}, @OA\Property(property="name", type="string"), @OA\Property(property="stream", type="string", nullable=true), @OA\Property(property="section", type="string", nullable=true), @OA\Property(property="class_teacher_id", type="string", format="uuid", nullable=true))
 */
class ClassRoomStoreRequestSchema {}

/**
 * @OA\Schema(schema="ClassRoomUpdateRequest", type="object", @OA\Property(property="name", type="string"), @OA\Property(property="stream", type="string", nullable=true), @OA\Property(property="section", type="string", nullable=true), @OA\Property(property="class_teacher_id", type="string", format="uuid", nullable=true))
 */
class ClassRoomUpdateRequestSchema {}

/**
 * @OA\Schema(schema="ClassRoomResource", type="object", @OA\Property(property="id", type="integer"), @OA\Property(property="name", type="string"), @OA\Property(property="stream", type="string", nullable=true), @OA\Property(property="section", type="string", nullable=true), @OA\Property(property="class_teacher_id", type="string", format="uuid", nullable=true), @OA\Property(property="class_teacher", type="object", nullable=true), @OA\Property(property="created_at", type="string", format="date-time"), @OA\Property(property="updated_at", type="string", format="date-time"))
 */
class ClassRoomResourceSchema {}

/**
 * @OA\Schema(schema="SubjectStoreRequest", type="object", required={"name","code"}, @OA\Property(property="name", type="string"), @OA\Property(property="code", type="string"), @OA\Property(property="is_core", type="boolean"))
 */
class SubjectStoreRequestSchema {}

/**
 * @OA\Schema(schema="SubjectUpdateRequest", type="object", @OA\Property(property="name", type="string"), @OA\Property(property="code", type="string"), @OA\Property(property="is_core", type="boolean"))
 */
class SubjectUpdateRequestSchema {}

/**
 * @OA\Schema(schema="SubjectResource", type="object", @OA\Property(property="id", type="integer"), @OA\Property(property="name", type="string"), @OA\Property(property="code", type="string"), @OA\Property(property="is_core", type="boolean"), @OA\Property(property="created_at", type="string", format="date-time"), @OA\Property(property="updated_at", type="string", format="date-time"))
 */
class SubjectResourceSchema {}

/**
 * @OA\Schema(
 *   schema="ClassSubjectTeacherAssignment",
 *   type="object",
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="teacher_id", type="string", format="uuid"),
 *   @OA\Property(property="is_core", type="boolean"),
 *   @OA\Property(property="starts_on", type="string", format="date"),
 *   @OA\Property(property="ends_on", type="string", format="date", nullable=true),
 *   @OA\Property(property="is_current", type="boolean"),
 *   @OA\Property(
 *     property="teacher",
 *     type="object",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="employee_number", type="string"),
 *     @OA\Property(property="first_name", type="string"),
 *     @OA\Property(property="last_name", type="string")
 *   )
 * )
 */
class ClassSubjectTeacherAssignmentSchema {}

/**
 * @OA\Schema(
 *   schema="ClassSubjectTeacherAssignmentInput",
 *   type="object",
 *   required={"teacher_id"},
 *   @OA\Property(property="teacher_id", type="string", format="uuid"),
 *   @OA\Property(property="is_core", type="boolean"),
 *   @OA\Property(property="starts_on", type="string", format="date"),
 *   @OA\Property(property="ends_on", type="string", format="date", nullable=true)
 * )
 */
class ClassSubjectTeacherAssignmentInputSchema {}

/**
 * @OA\Schema(
 *   schema="ClassSubjectStoreRequest",
 *   type="object",
 *   required={"class_room_id","subject_id"},
 *   @OA\Property(property="class_room_id", type="integer"),
 *   @OA\Property(property="subject_id", type="integer"),
 *   @OA\Property(property="teacher_assignments", type="array", @OA\Items(ref="#/components/schemas/ClassSubjectTeacherAssignmentInput"))
 * )
 */
class ClassSubjectStoreRequestSchema {}

/**
 * @OA\Schema(
 *   schema="ClassSubjectTeacherAssignRequest",
 *   type="object",
 *   required={"teacher_id"},
 *   @OA\Property(property="teacher_id", type="string", format="uuid"),
 *   @OA\Property(property="is_core", type="boolean"),
 *   @OA\Property(property="starts_on", type="string", format="date"),
 *   @OA\Property(property="ends_on", type="string", format="date", nullable=true)
 * )
 */
class ClassSubjectTeacherAssignRequestSchema {}

/**
 * @OA\Schema(
 *   schema="ClassSubjectResource",
 *   type="object",
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="class_room_id", type="integer"),
 *   @OA\Property(property="subject_id", type="integer"),
 *   @OA\Property(property="teacher_id", type="string", format="uuid", nullable=true),
 *   @OA\Property(property="core_teacher_id", type="string", format="uuid", nullable=true),
 *   @OA\Property(property="class_room", type="object"),
 *   @OA\Property(property="subject", ref="#/components/schemas/SubjectResource"),
 *   @OA\Property(property="core_teacher", type="object", nullable=true),
 *   @OA\Property(property="teacher_assignments", type="array", @OA\Items(ref="#/components/schemas/ClassSubjectTeacherAssignment")),
 *   @OA\Property(property="current_teacher_assignments", type="array", @OA\Items(ref="#/components/schemas/ClassSubjectTeacherAssignment")),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class ClassSubjectResourceSchema {}

/**
 * @OA\Schema(schema="AssessmentStoreRequest", type="object", required={"class_subject_id","term_id","assessment_type_id","title","max_score","assessment_date"}, @OA\Property(property="class_subject_id", type="integer"), @OA\Property(property="term_id", type="integer"), @OA\Property(property="assessment_type_id", type="integer"), @OA\Property(property="title", type="string"), @OA\Property(property="max_score", type="number", format="float"), @OA\Property(property="assessment_date", type="string", format="date"))
 */
class AssessmentStoreRequestSchema {}

/**
 * @OA\Schema(schema="AssessmentUpdateRequest", type="object", @OA\Property(property="title", type="string"), @OA\Property(property="max_score", type="number", format="float"), @OA\Property(property="assessment_date", type="string", format="date"))
 */
class AssessmentUpdateRequestSchema {}

/**
 * @OA\Schema(schema="AssessmentResource", type="object", @OA\Property(property="id", type="string", format="uuid"), @OA\Property(property="class_subject_id", type="integer"), @OA\Property(property="term_id", type="integer"), @OA\Property(property="assessment_type_id", type="integer"), @OA\Property(property="title", type="string"), @OA\Property(property="max_score", type="number", format="float"), @OA\Property(property="assessment_date", type="string", format="date"), @OA\Property(property="grades", type="array", @OA\Items(type="object")), @OA\Property(property="created_at", type="string", format="date-time"), @OA\Property(property="updated_at", type="string", format="date-time"))
 */
class AssessmentResourceSchema {}

/**
 * @OA\Schema(schema="RoleStoreRequest", type="object", required={"name"}, @OA\Property(property="name", type="string"), @OA\Property(property="description", type="string", nullable=true), @OA\Property(property="permission_ids", type="array", @OA\Items(type="integer")))
 */
class RoleStoreRequestSchema {}

/**
 * @OA\Schema(schema="RoleResource", type="object", @OA\Property(property="id", type="integer"), @OA\Property(property="name", type="string"), @OA\Property(property="guard_name", type="string"), @OA\Property(property="description", type="string", nullable=true), @OA\Property(property="permissions", type="array", @OA\Items(type="object")))
 */
class RoleResourceSchema {}

/**
 * @OA\Schema(schema="PermissionStoreRequest", type="object", required={"name"}, @OA\Property(property="name", type="string"), @OA\Property(property="description", type="string", nullable=true))
 */
class PermissionStoreRequestSchema {}

/**
 * @OA\Schema(schema="PermissionResource", type="object", @OA\Property(property="id", type="integer"), @OA\Property(property="name", type="string"), @OA\Property(property="guard_name", type="string"), @OA\Property(property="description", type="string", nullable=true))
 */
class PermissionResourceSchema {}

/**
 * @OA\Schema(schema="GradeStoreRequest", type="object", required={"student_id","assessment_id","score"}, @OA\Property(property="student_id", type="string", format="uuid"), @OA\Property(property="assessment_id", type="string", format="uuid"), @OA\Property(property="score", type="number", format="float"), @OA\Property(property="remarks", type="string", nullable=true))
 */
class GradeStoreRequestSchema {}

/**
 * @OA\Schema(schema="StudentGradeResource", type="object", @OA\Property(property="id", type="string", format="uuid"), @OA\Property(property="student_id", type="string", format="uuid"), @OA\Property(property="assessment_id", type="string", format="uuid"), @OA\Property(property="score", type="number", format="float"), @OA\Property(property="grade_letter", type="string"), @OA\Property(property="remarks", type="string", nullable=true), @OA\Property(property="recorded_by", type="string", format="uuid", nullable=true), @OA\Property(property="recorded_at", type="string", format="date-time", nullable=true), @OA\Property(property="student", type="object"), @OA\Property(property="assessment", type="object"), @OA\Property(property="created_at", type="string", format="date-time"), @OA\Property(property="updated_at", type="string", format="date-time"))
 */
class StudentGradeResourceSchema {}

/**
 * @OA\Schema(schema="GradeResource", type="object", @OA\Property(property="id", type="string", format="uuid"), @OA\Property(property="student_id", type="string", format="uuid"), @OA\Property(property="assessment_id", type="string", format="uuid"), @OA\Property(property="score", type="number", format="float"), @OA\Property(property="grade_letter", type="string"), @OA\Property(property="remarks", type="string", nullable=true), @OA\Property(property="recorded_by", type="string", format="uuid", nullable=true), @OA\Property(property="recorded_at", type="string", format="date-time", nullable=true), @OA\Property(property="student", type="object"), @OA\Property(property="assessment", type="object"), @OA\Property(property="created_at", type="string", format="date-time"), @OA\Property(property="updated_at", type="string", format="date-time"))
 */
class GradeResourceSchema {}

/**
 * @OA\Schema(schema="AttendanceStoreRequest", type="object", required={"student_id","class_room_id","attendance_date","status"}, @OA\Property(property="student_id", type="string", format="uuid"), @OA\Property(property="class_room_id", type="integer"), @OA\Property(property="attendance_date", type="string", format="date"), @OA\Property(property="status", type="string", enum={"PRESENT","ABSENT","LATE","EXCUSED"}))
 */
class AttendanceStoreRequestSchema {}

/**
 * @OA\Schema(schema="AttendanceResource", type="object", @OA\Property(property="id", type="string", format="uuid"), @OA\Property(property="student_id", type="string", format="uuid"), @OA\Property(property="class_room_id", type="integer"), @OA\Property(property="attendance_date", type="string", format="date"), @OA\Property(property="status", type="string"), @OA\Property(property="recorded_by", type="string", format="uuid", nullable=true), @OA\Property(property="student", type="object"), @OA\Property(property="class_room", type="object"), @OA\Property(property="created_at", type="string", format="date-time"), @OA\Property(property="updated_at", type="string", format="date-time"))
 */
class AttendanceResourceSchema {}

/**
 * @OA\Schema(schema="PaymentStoreRequest", type="object", required={"student_fee_account_id","amount_paid","payment_date","receipt_number"}, @OA\Property(property="student_fee_account_id", type="string", format="uuid"), @OA\Property(property="amount_paid", type="number", format="float"), @OA\Property(property="payment_date", type="string", format="date"), @OA\Property(property="payment_method", type="string", nullable=true), @OA\Property(property="receipt_number", type="string"))
 */
class PaymentStoreRequestSchema {}

/**
 * @OA\Schema(schema="PaymentResource", type="object", @OA\Property(property="id", type="string", format="uuid"), @OA\Property(property="student_fee_account_id", type="string", format="uuid"), @OA\Property(property="amount_paid", type="number", format="float"), @OA\Property(property="payment_date", type="string", format="date"), @OA\Property(property="payment_method", type="string", nullable=true), @OA\Property(property="receipt_number", type="string"), @OA\Property(property="recorded_by", type="string", format="uuid", nullable=true), @OA\Property(property="account", type="object"), @OA\Property(property="created_at", type="string", format="date-time"), @OA\Property(property="updated_at", type="string", format="date-time"))
 */
class PaymentResourceSchema {}

/**
 * @OA\Schema(schema="StudentFeesResponse", type="object", @OA\Property(property="id", type="string", format="uuid"), @OA\Property(property="admission_number", type="string"), @OA\Property(property="first_name", type="string"), @OA\Property(property="last_name", type="string"), @OA\Property(property="feeAccounts", type="array", @OA\Items(type="object")))
 */
class StudentFeesResponseSchema {}

/**
 * @OA\Schema(schema="CollectionsReportItem", type="object", @OA\Property(property="payment_date", type="string", format="date"), @OA\Property(property="payment_method", type="string", nullable=true), @OA\Property(property="total_collected", type="number", format="float"), @OA\Property(property="payments_count", type="integer"))
 */
class CollectionsReportItemSchema {}

/**
 * @OA\Schema(schema="ReportCardResponse", type="object", @OA\Property(property="student", type="object"), @OA\Property(property="summary", type="object"), @OA\Property(property="subjects", type="array", @OA\Items(type="object")), @OA\Property(property="grade_history", type="array", @OA\Items(type="object")))
 */
class ReportCardResponseSchema {}

/**
 * @OA\Schema(
 *   schema="PaginationMeta",
 *   type="object",
 *   @OA\Property(property="current_page", type="integer", example=1),
 *   @OA\Property(property="from", type="integer", nullable=true, example=1),
 *   @OA\Property(property="last_page", type="integer", example=10),
 *   @OA\Property(property="path", type="string", format="uri", example="/api/v1/students"),
 *   @OA\Property(property="per_page", type="integer", example=10),
 *   @OA\Property(property="to", type="integer", nullable=true, example=10),
 *   @OA\Property(property="total", type="integer", example=100)
 * )
 */
class PaginationMetaSchema {}

/**
 * @OA\Schema(
 *   schema="PaginationLinks",
 *   type="object",
 *   @OA\Property(property="first", type="string", format="uri", example="/api/v1/students?page=1"),
 *   @OA\Property(property="last", type="string", format="uri", example="/api/v1/students?page=10"),
 *   @OA\Property(property="prev", type="string", format="uri", nullable=true, example=null),
 *   @OA\Property(property="next", type="string", format="uri", nullable=true, example="/api/v1/students?page=2")
 * )
 */
class PaginationLinksSchema {}

/**
 * @OA\Schema(
 *   schema="PaginatedStudentResponse",
 *   type="object",
 *   @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/StudentResource")),
 *   @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta"),
 *   @OA\Property(property="links", ref="#/components/schemas/PaginationLinks")
 * )
 */
class PaginatedStudentResponseSchema {}

/**
 * @OA\Schema(
 *   schema="PaginatedStaffResponse",
 *   type="object",
 *   @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/StaffResource")),
 *   @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta"),
 *   @OA\Property(property="links", ref="#/components/schemas/PaginationLinks")
 * )
 */
class PaginatedStaffResponseSchema {}

/**
 * @OA\Schema(
 *   schema="PaginatedClassRoomResponse",
 *   type="object",
 *   @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ClassRoomResource")),
 *   @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta"),
 *   @OA\Property(property="links", ref="#/components/schemas/PaginationLinks")
 * )
 */
class PaginatedClassRoomResponseSchema {}

/**
 * @OA\Schema(
 *   schema="PaginatedPaymentResponse",
 *   type="object",
 *   @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PaymentResource")),
 *   @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta"),
 *   @OA\Property(property="links", ref="#/components/schemas/PaginationLinks")
 * )
 */
class PaginatedPaymentResponseSchema {}

/**
 * @OA\Schema(
 *   schema="PaginatedAssessmentResponse",
 *   type="object",
 *   @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AssessmentResource")),
 *   @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta"),
 *   @OA\Property(property="links", ref="#/components/schemas/PaginationLinks")
 * )
 */
class PaginatedAssessmentResponseSchema {}

/**
 * @OA\Schema(
 *   schema="PaginatedClassSubjectResponse",
 *   type="object",
 *   @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ClassSubjectResource")),
 *   @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta"),
 *   @OA\Property(property="links", ref="#/components/schemas/PaginationLinks")
 * )
 */
class PaginatedClassSubjectResponseSchema {}

/**
 * @OA\Schema(
 *   schema="PaginatedGradeResponse",
 *   type="object",
 *   @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/GradeResource")),
 *   @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta"),
 *   @OA\Property(property="links", ref="#/components/schemas/PaginationLinks")
 * )
 */
class PaginatedGradeResponseSchema {}

/**
 * @OA\Schema(
 *   schema="PaginatedAttendanceResponse",
 *   type="object",
 *   @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AttendanceResource")),
 *   @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta"),
 *   @OA\Property(property="links", ref="#/components/schemas/PaginationLinks")
 * )
 */
class PaginatedAttendanceResponseSchema {}
