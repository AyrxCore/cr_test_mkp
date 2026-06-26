<?php

declare(strict_types=1);

namespace App\Dto;

final class LegalContent
{
    private ?string $cgu = null;

    private ?string $legalTerms = null;

    private ?string $privacyPolicy = null;

    public function getCgu(): ?string
    {
        return $this->cgu;
    }

    public function setCgu(?string $cgu): void
    {
        $this->cgu = $cgu;
    }

    public function getLegalTerms(): ?string
    {
        return $this->legalTerms;
    }

    public function setLegalTerms(?string $legalTerms): void
    {
        $this->legalTerms = $legalTerms;
    }

    public function getPrivacyPolicy(): ?string
    {
        return $this->privacyPolicy;
    }

    public function setPrivacyPolicy(?string $privacyPolicy): void
    {
        $this->privacyPolicy = $privacyPolicy;
    }
}
