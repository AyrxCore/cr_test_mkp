<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PasswordStrengthValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PasswordStrength) {
            throw new UnexpectedTypeException($constraint, PasswordStrength::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$this->isValid((string) $value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }

    private function isValid(string $password): bool
    {
        return \strlen($password) >= 12
            && \preg_match('/[A-Z]/', $password)
            && \preg_match('/[a-z]/', $password)
            && \preg_match('/[0-9]/', $password)
            && \preg_match('/[^A-Za-z0-9]/', $password);
    }
}
