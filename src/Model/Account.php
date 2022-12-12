<?php
namespace App\Model;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Dto\UserAccountInputDto;
use App\State\UserAccountProcessor;
use Symfony\Component\Uid\Uuid;
#[ApiResource(
    collectionOperations: ['post' => [

    ]],
    itemOperations: []
)]
#[Post(input: UserAccountInputDto::class, processor: UserAccountProcessor::class)]
final class Account {}
