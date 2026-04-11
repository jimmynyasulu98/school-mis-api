# High School MIS ER Diagram

```mermaid
erDiagram
    STUDENTS ||--o{ STUDENT_GUARDIAN : has
    GUARDIANS ||--o{ STUDENT_GUARDIAN : linked_to
    STAFF ||--o{ STAFF_ROLE : assigned
    ROLES ||--o{ STAFF_ROLE : grants
    ROLES ||--o{ ROLE_PERMISSION : maps
    PERMISSIONS ||--o{ ROLE_PERMISSION : attached
    ACADEMIC_YEARS ||--o{ TERMS : contains
    CLASS_ROOMS ||--o{ CLASS_SUBJECTS : offers
    SUBJECTS ||--o{ CLASS_SUBJECTS : mapped
    STAFF ||--o{ CLASS_SUBJECTS : teaches
    STUDENTS ||--o{ CLASS_ENROLLMENTS : enrolls
    CLASS_ROOMS ||--o{ CLASS_ENROLLMENTS : receives
    TERMS ||--o{ CLASS_ENROLLMENTS : scoped_by
    CLASS_SUBJECTS ||--o{ ASSESSMENTS : schedules
    TERMS ||--o{ ASSESSMENTS : contains
    ASSESSMENT_TYPES ||--o{ ASSESSMENTS : categorizes
    STUDENTS ||--o{ STUDENT_GRADES : earns
    ASSESSMENTS ||--o{ STUDENT_GRADES : yields
    STUDENTS ||--o{ STUDENT_GRADE_HISTORIES : tracks
    CLASS_ROOMS ||--o{ FEE_STRUCTURES : priced_for
    TERMS ||--o{ FEE_STRUCTURES : billed_in
    FEE_STRUCTURES ||--o{ FEE_ITEMS : broken_into
    STUDENTS ||--o{ STUDENT_FEE_ACCOUNTS : billed
    FEE_STRUCTURES ||--o{ STUDENT_FEE_ACCOUNTS : applied
    STUDENT_FEE_ACCOUNTS ||--o{ PAYMENTS : settles
    STAFF ||--o{ PAYMENTS : records
    STAFF ||--o{ STAFF_ASSESSMENTS : evaluated
    STAFF ||--o{ STAFF_ASSESSMENT_HISTORIES : summarized
    USERS ||--o{ AUDIT_LOGS : creates
```

## Notes

- `users` can represent either a staff member or a guardian.
- `class_subjects` resolves the many-to-many relationship between classes and subjects and stores the assigned teacher.
- `student_grade_histories` stores final promotion and annual performance snapshots.
- `audit_logs` captures security-sensitive actions such as login, grade edits, and finance changes.
