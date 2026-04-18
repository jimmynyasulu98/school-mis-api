# Assessment Authorization

This project now uses Laravel policies for assessment creation and editing, layered on top of the existing permission middleware.

## Business Rules

1. A user must still have the base permission for the endpoint.
   `assessments.create` is required for creation.
   `assessments.edit` is required for updates.
2. Users with `assessments.manage` bypass the finer-grained checks.
3. For normal assessment types, a user must be the teacher assigned to the target `class_subject`.
4. For restricted assessment types, the type itself declares the extra permission required to create or update it.
   `End Of Term Exam` uses `assessments.create.end-of-term`.

## Why This Is Scalable

Static CRUD permissions are still useful, but they are not enough for rules like:

- one type can be created school-wide
- another type can only be created by assigned teachers
- a future type may need a different approval permission

By storing the extra requirement on `assessment_types.creation_permission`, the restriction travels with the type definition instead of being duplicated across controllers.

## Data Model

The `assessment_types` table now includes:

- `code`: stable machine-readable identifier
- `creation_permission`: nullable permission name required for restricted types

Example seeded data:

- `continuous_test` -> unrestricted
- `mid_term_exam` -> unrestricted
- `end_of_term_exam` -> requires `assessments.create.end-of-term`

## How Authorization Works

`App\Policies\AssessmentPolicy` is registered for the `Assessment` model.

Create flow:

1. Route middleware checks `assessments.create`.
2. Controller loads the requested `AssessmentType` and `ClassSubject`.
3. Policy decides:
   if user has `assessments.manage`, allow.
   if type has `creation_permission`, require that permission.
   otherwise require the user to be the assigned teacher for the `class_subject`.

Update flow:

1. Route middleware checks `assessments.edit`.
2. Policy applies the same restricted-type rule.
3. For unrestricted types, the user must be assigned to both the current and target `class_subject`.

## Granting End Of Term Access To Selected People

You can grant the special permission directly to a user, or via a role.

Example with a user instance:

```php
$user->givePermissionTo('assessments.create.end-of-term');
```

Example with a role:

```php
$role->givePermissionTo('assessments.create.end-of-term');
$user->assignRole($role);
```

This is the recommended way to allow principals, heads of department, academic office staff, or exam coordinators to create end-of-term assessments without giving them full assessment management rights.

## Deployment Steps

1. Run migrations.
```bash
php artisan migrate --force
```
2. Refresh seed data so the new permission and assessment type metadata exist.
```bash
php artisan db:seed --class=RbacSeeder --force
php artisan db:seed --class=ReferenceDataSeeder --force
```
3. Run the test suite.
```bash
php artisan test --filter=AssessmentAuthorizationTest
```

## API Payload

Create assessment request:

```json
{
  "assessment_type_id": 1,
  "class_subject_id": 1,
  "term_id": 1,
  "title": "Term 1 Final Exam",
  "max_score": 100,
  "assessment_date": "2026-04-20"
}
```

## Notes

- The assessment API was aligned with the actual schema in the database.
- The assessment resource now returns `assessment_type`, `class_subject`, `term`, and derived nested context for class room, subject, and academic year.
- If you later add another restricted assessment type, set `creation_permission` on that row and seed or grant the matching permission. No controller rewrite is needed.
