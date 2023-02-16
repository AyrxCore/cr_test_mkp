<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Scalar;
use Symfony\Component\Validator\Constraints as Assert;
#[ApiResource(
    collectionOperations: [],
    itemOperations: [
        'get',
        'update' => [
            "openapi_context" => [
                'summary' => 'Editer un account',
                'description' => "Permet d'enregistrer des modifications dans un account uppler"
            ],
            "method" => "PATCH",
            "validate" => true,
        ]
    ]
)]
final class SubAccount
{
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    #[Assert\Type("string", message: "(email) string required")]
    private ?string $email = null;

    #[Assert\Type("string", message: "(lastname) string required")]
    private ?string $lastName = null;

    #[Assert\Type("string", message: "(firstname) string required")]
    private ?string $firstName = null;

    #[Assert\Type("string", message: "(phone) string required")]
    private ?string $phone = null;

    #[Assert\Type("integer", message: "(shipping_address_id) Integer required")]
    private ?int $shippingAddressId = null;

    #[Assert\Type("integer", message: "(billing_address_id) Integer required")]
    private ?int $billingAddressId = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getShippingAddressId(): ?int
    {
        return $this->shippingAddressId;
    }

    public function setShippingAddressId(int $shippingAddressId): ?self
    {
        $this->shippingAddressId = $shippingAddressId;

        return $this;
    }

    public function getBillingAddressId(): ?int
    {
        return $this->billingAddressId;
    }

    public function setBillingAddressId(int $billingAddressId): ?self
    {
        $this->billingAddressId = $billingAddressId;

        return $this;
    }
}
