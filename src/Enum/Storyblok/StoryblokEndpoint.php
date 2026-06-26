<?php

declare(strict_types=1);

namespace App\Enum\Storyblok;

enum StoryblokEndpoint: string
{
    case ACCORD_CADRE = 'accord-cadre/';
    case NEWS = 'news/';
    case LEGAL_CONTENT = 'legal-content/';
}
