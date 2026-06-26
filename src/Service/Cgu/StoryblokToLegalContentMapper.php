<?php

declare(strict_types=1);

namespace App\Service\Cgu;

use App\Dto\LegalContent;
use App\Service\Storyblok\StoryblokRichTextResolver;

class StoryblokToLegalContentMapper
{
    private const string KEY_STORIES = 'stories';
    private const string KEY_NAME = 'name';
    private const string KEY_CONTENT = 'content';
    private const string KEY_CGU = 'cgu';
    private const string KEY_LEGAL_TERMS = 'legalTerms';
    private const string KEY_PRIVACY_POLICY = 'privacyPolicy';

    public function __construct(
        private readonly StoryblokRichTextResolver $richTextResolver,
    ) {
    }

    public function mapByChannelCode(array $storyblokResponse, string $channelCode): ?LegalContent
    {
        $stories = $storyblokResponse[self::KEY_STORIES] ?? [];

        foreach ($stories as $story) {
            if (($story[self::KEY_NAME] ?? '') === $channelCode) {
                return $this->mapLegalContent($story);
            }
        }

        return null;
    }

    public function mapLegalContent(array $storyblokData): LegalContent
    {
        $legalContent = new LegalContent();

        $contentData = $storyblokData[self::KEY_CONTENT] ?? [];
        $legalContent->setCgu($this->resolveRichText($contentData[self::KEY_CGU] ?? null));
        $legalContent->setLegalTerms($this->resolveRichText($contentData[self::KEY_LEGAL_TERMS] ?? null));
        $legalContent->setPrivacyPolicy($this->resolveRichText($contentData[self::KEY_PRIVACY_POLICY] ?? null));

        return $legalContent;
    }

    private function resolveRichText(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (\is_array($value)) {
            $rendered = $this->richTextResolver->render($value);

            return $rendered !== null ? \html_entity_decode($rendered) : null;
        }

        return \is_string($value) ? $value : null;
    }
}
