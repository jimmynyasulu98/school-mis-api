# Student Enrollment & Term Management System

## Overview

This system handles the complete lifecycle of student enrollment across academic years and terms, with built-in support for:
- Automatic enrollment of promoted students when creating a new academic year
- Manual enrollment of failed students with flexibility
- Student deactivation when they leave or fail
- Audit trail for all enrollment changes
- Configurable options to handle different school policies

---

## System Architecture

### Core Components

1. **StudentEnrollmentService** - Business logic for enrollment operations
2. **Student Model** - Enhanced with status tracking and helper methods
3. **ClassEnrollment Model** - Tracks student enrollment per term with enrollment type
4. **StudentEnrollmentController** - API endpoints for enrollment management
5. **Database Migrations** - New fields and relationships

---

## Student Status Values

```php
ACTIVE       // Currently enrolled, attending school
INACTIVE     // Deactivated (left school, not returning)
FAILED       // Failed a term, not yet re-enrolled
PROMOTED     // Successfully promoted to next class
TRANSFERRED  // Moved to another school
WITHDRAWN    // Graduated/completed final year
```

---

## Enrollment Types

```php
AUTO         // Automatically added when first term of new year is created
MANUAL       // Manually added by admin
REPEAT       // Failed student repeating same/lower class
TRANSFER     // Student moving to different track/class
```

---

## Usage Workflows

### Scenario 1: Creating First Term of New Academic Year

**API Request:**
```json
POST /api/v1/terms
{
  "academic_year_id": "uuid",
  "name": "Term 1",
  "start_date": "2026-01-01",
  "end_date": "2026-04-30",
  "is_current": true,
  "auto_enroll_students": true,
  "skip_final_year": true,
  "include_failed_students": false
}
```

**What Happens:**
1. Term is created
2. Service fetches all students from previous year
3. **Skips** if:
   - Student is inactive
   - Student is in final year (Form 6)
   - Student failed AND `include_failed_students=false`
4. **Automatically enrolls**:
   - Determines next class based on current form
   - Creates ClassEnrollment with `enrollment_type=AUTO`
   - Marks student as `PROMOTED`
   - Logs audit trail
5. Returns summary:
```json
{
  "data": { "id": "...", "name": "Term 1", ... },
  "enrollment_summary": {
    "total_enrolled": 45,
    "total_skipped": 3,
    "enrolled": [
      {
        "student_id": "...",
        "name": "John Doe",
        "from_class": "Form 1A",
        "to_class": "Form 2A"
      }
    ],
    "skipped": [
      {
        "student_id": "...",
        "reason": "Student is in final year"
      }
    ]
  }
}
```

---

### Scenario 2: Enrolling Failed Students Manually

**Step 1: Check Which Students Failed**
```sql
SELECT * FROM students WHERE status = 'FAILED' AND is_active = true
```

**API Request:**
```json
POST /api/v1/students/{student_id}/enroll-failed
{
  "class_room_id": 2,
  "term_id": "uuid",
  "enrollment_type": "REPEAT",
  "reason": "School decided to give student another chance to improve"
}
```

**What Happens:**
1. Service validates student is marked as FAILED
2. Checks if student not already enrolled in this term
3. Creates ClassEnrollment with `enrollment_type=REPEAT`
4. Changes student status from FAILED → ACTIVE
5. Logs audit entry with reason
6. Returns enrollment details

**Response:**
```json
{
  "message": "Student enrolled successfully",
  "data": { "id": "...", "first_name": "Jane", "status": "ACTIVE", ... },
  "enrollment": {
    "enrollment_id": "...",
    "class_name": "Form 1B",
    "form": "Form 1",
    "term_name": "Term 1",
    "enrollment_type": "REPEAT",
    "reason": "School decided to give student another chance to improve"
  }
}
```

---

### Scenario 3: Deactivating a Student

**API Request:**
```json
POST /api/v1/students/{student_id}/deactivate
{
  "reason": "left_school",
  "notes": "Student moved to another country"
}
```

**Valid Reasons:**
- `left_school` - Left voluntarily
- `failed_not_returning` - Failed and won't return
- `transferred_school` - Moved to another school
- `withdrawn_by_guardian` - Guardian requested withdrawal
- `other` - Other reason

**What Happens:**
1. Service checks student is currently active
2. Updates `is_active=false` and `status=INACTIVE`
3. Logs audit entry with reason and notes
4. Student won't be auto-enrolled in future terms

**Response:**
```json
{
  "message": "Student deactivated successfully",
  "data": { "id": "...", "is_active": false, "status": "INACTIVE", ... }
}
```

---

### Scenario 4: View Enrollment History

**API Request:**
```
GET /api/v1/students/{student_id}/enrollments
```

**Response:**
```json
{
  "data": [
    {
      "id": "enrollment-uuid",
      "class_name": "Form 2A",
      "form": "Form 2",
      "stream": "A",
      "term_name": "Term 1",
      "academic_year": "2026",
      "enrollment_type": "AUTO",
      "status": "ACTIVE",
      "enrollment_date": "2026-01-01",
      "promotion_reason": "Auto-promoted from previous year"
    },
    {
      "id": "enrollment-uuid",
      "class_name": "Form 1A",
      "form": "Form 1",
      "stream": "A",
      "term_name": "Term 3",
      "academic_year": "2025",
      "enrollment_type": "MANUAL",
      "status": "PROMOTED",
      "enrollment_date": "2025-08-01",
      "promotion_reason": null
    }
  ]
}
```

---

## Helper Methods in Student Model

```php
// Check if student is in final year
$student->isInFinalYear();  // true/false

// Check if student can be promoted
$student->canBePromoted();  // true/false

// Deactivate a student with reason
$student->deactivate('Student moved abroad');

// Get all enrollments
$student->enrollments;  // Collection

// Get current classroom
$student->currentClassRoom;  // ClassRoom model
```

---

## Database Fields Added

### Students Table
```sql
is_active BOOLEAN DEFAULT true
- true: Student can be enrolled
- false: Student is inactive (left, failed permanently, etc.)
```

### ClassEnrollments Table
```sql
enrollment_type VARCHAR(50) DEFAULT 'MANUAL'
- AUTO: Auto-enrolled during term creation
- MANUAL: Manually enrolled by admin
- REPEAT: Failed student repeating year
- TRANSFER: Student moving to different class

promoted_from_class_id BIGINT UNSIGNED NULLABLE
- References previous class (for promotion tracking)

enrolled_by UUID NULLABLE
- User who created the enrollment (foreign key to users)

promotion_reason TEXT NULLABLE
- Why student was enrolled (auto-promotion, manual re-enrollment, etc.)
```

---

## Handling Edge Cases

### Case 1: Student Enrolled but Changed Grades
**Problem**: Student was auto-enrolled but grades changed (now should fail)
**Solution**: 
1. Update student status to FAILED manually
2. Create manual enrollment for next term with reason explaining the correction

### Case 2: Student Transfers Mid-Year
**Problem**: Student leaves in middle of term
**Solution**:
1. Deactivate student (reason: transferred_school)
2. Don't manually create enrollment for next term
3. Student won't appear in auto-enrollment

### Case 3: Student Needs to Repeat But In Lower Class
**Problem**: Student failed badly, needs more than one-year repeat
**Solution**:
```json
POST /api/v1/students/{student_id}/enroll-failed
{
  "class_room_id": 1,
  "term_id": "uuid",
  "enrollment_type": "REPEAT",
  "reason": "Failed Form 2, needs to repeat Form 1 for foundational concepts"
}
```

### Case 4: Re-activating an Inactive Student
**Current**: System marks as INACTIVE (soft-delete)
**Recommended Action**:
```php
// Manually update student
$student->update([
    'is_active' => true,
    'status' => Student::STATUS_ACTIVE
]);

// Then manually enroll for next term
// Call enrollFailedStudent endpoint with approval notes
```

### Case 5: Audit Trail for Compliance
All enrollment changes are logged in `audit_logs` table:
```json
{
  "action": "PROMOTED",
  "subject_type": "Student",
  "subject_id": "student_uuid",
  "user_id": "admin_uuid",
  "notes": {
    "from_class": "Form 1A",
    "to_class": "Form 2A",
    "term_id": "term_uuid"
  },
  "created_at": "2026-01-01T12:00:00Z"
}
```

---

## Best Practices

### ✅ DO:

1. **Always set `auto_enroll_students=true`** when creating first term
   - Reduces manual work
   - Consistent promotion logic
   - Better audit trail

2. **Use enrollment_type consistently**
   - AUTO for automatic promotion
   - REPEAT for failed students
   - TRANSFER for special cases

3. **Document deactivation reasons**
   - Helps auditing
   - Explains decisions
   - Supports data analysis

4. **Check student history before enrollment**
   - Use `/students/{student_id}/enrollments` endpoint
   - Avoid duplicate enrollments
   - Understand student journey

5. **Create backup before bulk operations**
   - Test with small group first
   - Have rollback plan
   - Monitor audit logs

### ❌ DON'T:

1. **Don't hard-delete students**
   - Use deactivation instead
   - Preserves history
   - Maintains audit trail

2. **Don't change student status directly**
   - Use enrollment service
   - Keeps audit trail
   - Ensures consistency

3. **Don't auto-enroll failed students by default**
   - Requires manual decision
   - Allows time for review
   - Flexibility for exceptions

4. **Don't forget to log deactivations**
   - Always provide reason
   - Helps future audits
   - Maintains institutional knowledge

---

## Migration Instructions

```bash
# Run migration to add new fields
php artisan migrate

# Test with a small group first
php artisan tinker

# Check AuditLog model exists and migrations are complete
```

---

## Permissions Required

Add these permissions to your roles:

```php
'students.manage'  // Can enroll failed students, deactivate students
'students.create'  // Can create new students
'students.view'    // Can view students
'students.edit'    // Can update students
'terms.create'     // Can create terms with auto-enrollment
```

---

## Future Enhancements

1. **Batch Operations**
   - Bulk deactivate students
   - Bulk re-activate with conditions

2. **Conditional Auto-Enrollment**
   - Minimum GPA requirement
   - Attendance threshold
   - Behavior record checks

3. **Appeal System**
   - Allow failed students to appeal
   - Track appeal decisions
   - Auto-enroll on appeal approval

4. **Analytics Dashboard**
   - Promotion/failure rates
   - Deactivation reasons analysis
   - Re-enrollment success rates

5. **Integration with Assessment**
   - Auto-mark failed if grades < threshold
   - Trigger notifications
   - Create promotion recommendations

