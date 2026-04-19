# Class Subject Assignments

This module separates the academic offering from the teaching assignments:

- `class_subjects` identifies that a subject is taught in a specific class.
- `class_subject_teachers` stores one or more teachers attached to that class-subject.
- `class_subjects.teacher_id` is kept as the primary or core teacher for backward compatibility and quick lookups.

## Why this shape

It supports the normal case of one teacher per subject per class, but also supports:

- team teaching
- handover between teachers during a term
- a core teacher for ownership, reporting, and default workflow decisions

## API endpoints

- `GET /api/v1/class-subjects`
- `POST /api/v1/class-subjects`
- `GET /api/v1/class-subjects/{classSubject}`
- `DELETE /api/v1/class-subjects/{classSubject}`
- `POST /api/v1/class-subjects/{classSubject}/teachers`
- `PATCH /api/v1/class-subjects/{classSubject}/teachers/{teacher}/core`
- `DELETE /api/v1/class-subjects/{classSubject}/teachers/{teacher}`

## Create a class subject with two teachers

```json
{
  "class_room_id": 1,
  "subject_id": 2,
  "teacher_assignments": [
    {
      "teacher_id": "11111111-1111-1111-1111-111111111111",
      "is_core": true
    },
    {
      "teacher_id": "22222222-2222-2222-2222-222222222222",
      "is_core": false
    }
  ]
}
```

## Behavior rules

- A class can only have one `class_subject` per subject.
- A class-subject can have many teachers.
- Only one teacher can be marked as core at a time.
- If no teacher is marked as core during creation, the first teacher is promoted automatically.
- If the current core teacher is unassigned and another teacher remains, one remaining teacher is promoted automatically.

## Switch the core teacher explicitly

No request body is needed. Promote an already-assigned teacher with:

`PATCH /api/v1/class-subjects/{classSubject}/teachers/{teacher}/core`

## Recommendation

Use the core teacher as the operational owner for:

- moderation and signoff
- report defaults
- timetable ownership
- accountability when there is a dispute or missing marks

Allow co-teachers to create and manage routine assessments unless your school policy explicitly wants assessment creation restricted to the core teacher only. That keeps team teaching practical while preserving a clear owner.
