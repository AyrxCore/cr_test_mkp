<?php

declare(strict_types=1);

namespace App\Enum\Storyblok;

enum AccordCadreBlock: string
{
    case BANNER = 'bannerBlock';
    case PRESENTATION = 'presentationBlock';
    case NEGOCIATED_TERMS = 'negociatedTermsBlock';
    case STEPS = 'stepsBlock';
}
