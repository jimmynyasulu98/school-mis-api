<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\AssessmentType;
use App\Models\ClassSubject;
use App\Models\User;

class AssessmentPolicy
{
    public function view(User $user, Assessment $assessment): bool
    {
        if ($user->can('assessments.manage')) {
            return true;
        }

        return $user->can('assessments.view');
    }

    public function create(User $user, AssessmentType $assessmentType, ClassSubject $classSubject): bool
    {
        if (! $user->can('assessments.create')) {
            return false;
        }

        if ($user->can('assessments.manage')) {
            return true;
        }

        $requiredPermission = $assessmentType->creation_permission;

        if ($requiredPermission !== null) {
            return $user->can($requiredPermission);
        }

        return $user->isAssignedTeacherForClassSubject($classSubject);
    }

    public function update(User $user, Assessment $assessment, AssessmentType $assessmentType, ClassSubject $classSubject): bool
    {
        if (! $user->can('assessments.edit')) {
            return false;
        }

        if ($user->can('assessments.manage')) {
            return true;
        }

        $requiredPermission = $assessmentType->creation_permission;

        if ($requiredPermission !== null) {
            return $user->can($requiredPermission);
        }

        return $user->isAssignedTeacherForClassSubject($classSubject)
            && $user->isAssignedTeacherForClassSubject($assessment->classSubject);
    }
}
