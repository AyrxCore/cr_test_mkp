<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class PasswordStrength extends Constraint
{
    public string $message = 'Le mot de passe ne respecte pas les consignes de sécurité (12 caractères minimum, une majuscule, une minuscule, un chiffre, un caractère spécial).';
}
