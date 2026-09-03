<?php

declare(strict_types=1);

namespace App\Service\SemanticButton;

use App\Dto\SemanticButton\SemanticButton;

class StoryblokToSemanticButtonMapper
{
    private const string KEY_STORIES = 'stories';
    private const string KEY_NAME = 'name';
    private const string KEY_CONTENT = 'content';
    private const string KEY_SECTION_TITLE = 'sectionTitle';
    private const string KEY_SEMANTIC_BUTTON = 'semanticButton';
    private const string KEY_LABEL = 'label';
    private const string KEY_SEARCH = 'search';
    private const string KEY_POSITION = 'position';

    /**
     * @return SemanticButton[]
     */
    public function mapByChannelCode(array $storyblokResponse, string $channelCode): array
    {
        $stories = $storyblokResponse[self::KEY_STORIES] ?? [];

        foreach ($stories as $story) {
            if (($story[self::KEY_NAME] ?? '') === $channelCode) {
                return $this->mapSemanticButtons($story);
            }
        }

        return [];
    }

    /**
     * @return SemanticButton[]
     */
    public function mapSemanticButtons(array $storyblokData): array
    {
        $contentData = $storyblokData[self::KEY_CONTENT] ?? [];
        $semanticButtons = [];
        $nextId = 0;

        $sectionTitle = $contentData[self::KEY_SECTION_TITLE] ?? null;
        if (\is_string($sectionTitle) && $sectionTitle !== '') {
            $sectionTitleButton = new SemanticButton();
            $sectionTitleButton->setId($nextId++);
            $sectionTitleButton->setSectionTitle($sectionTitle);
            $semanticButtons[] = $sectionTitleButton;
        }

        $buttons = $contentData[self::KEY_SEMANTIC_BUTTON] ?? [];
        if (\is_array($buttons)) {
            foreach ($buttons as $buttonData) {
                if (!\is_array($buttonData)) {
                    continue;
                }

                $semanticButton = new SemanticButton();
                $semanticButton->setId($nextId++);
                $semanticButton->setLabel($buttonData[self::KEY_LABEL] ?? null);
                $semanticButton->setSearch($buttonData[self::KEY_SEARCH] ?? null);
                $semanticButton->setPosition((int) $buttonData[self::KEY_POSITION]);
                $semanticButtons[] = $semanticButton;
            }
        }

        usort(
            $semanticButtons,
            fn (SemanticButton $a, SemanticButton $b) => $a->getPosition() > $b->getPosition(),
        );

        return $semanticButtons;
    }
}
