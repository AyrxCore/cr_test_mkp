<?php

declare(strict_types=1);

namespace App\Service\AccordCadre;

use App\Context\ChannelContext;
use App\Dto\AccordCadre\AccordCadreContent;
use App\Dto\AccordCadre\ListBlocks\BannerBlockContent;
use App\Dto\AccordCadre\ListBlocks\Components\AssetButton;
use App\Dto\AccordCadre\ListBlocks\Components\ImageItem;
use App\Dto\AccordCadre\ListBlocks\Components\StepItem;
use App\Dto\AccordCadre\ListBlocks\NegociatedTermsBlockContent;
use App\Dto\AccordCadre\ListBlocks\PresentationBlockContent;
use App\Dto\AccordCadre\ListBlocks\StepsBlockContent;
use App\Enum\Djust\DjustAccordCadreType;
use App\Enum\Storyblok\AccordCadreBlock;
use App\Helper\Formatter\PhoneFormatter;
use App\Service\Storyblok\StoryblokRichTextResolver;

class StoryblokToAccordCadreMapper
{
    private const string KEY_STORIES = 'stories';
    private const string KEY_CONTENT = 'content';
    private const string KEY_BODY = 'body';
    private const string KEY_COMPONENT = 'component';
    private const string KEY_ACCORD_CADRE_NAME = 'accordCadreName';
    private const string KEY_ACCORD_CADRE_TYPE = 'accordCadreType';
    private const string KEY_LABEL_NOT_ACTIVATED = 'labelNotActivated';
    private const string KEY_LABEL_PENDING = 'labelPending';
    private const string KEY_LABEL_ACTIVATED = 'labelActivated';
    private const string KEY_CONFIRMATION_LAYER_DESCRIPTION = 'confirmationLayerDescription';
    private const string KEY_SUCCESS_LAYER_DESCRIPTION = 'successLayerDescription';
    private const string KEY_TARIF_ID = 'tarifId';
    private const string KEY_LABEL_CTA_RATTACHEMENT = 'labelCtaRattachement';
    private const string KEY_URL_CTA_RATTACHEMENT = 'urlCtaRattachement';
    private const string KEY_IMG_BANNER_URL_DESKTOP = 'imgBannerUrlDesktop';
    private const string KEY_IMG_BANNER_URL_MOBILE = 'imgBannerUrlMobile';
    private const string KEY_LOGO_URL = 'logoUrl';
    private const string KEY_BADGE_TEXT_TOP = 'badgeTextTop';
    private const string KEY_BADGE_TEXT_BOTTOM = 'badgeTextBottom';
    private const string KEY_TITLE = 'title';
    private const string KEY_TITLE_QANTIS = 'title_qantis';
    private const string KEY_TITLE_WHITELABEL = 'title_whitelabel';
    private const string KEY_DESCRIPTION = 'description';
    private const string KEY_DESCRIPTION_QANTIS = 'description_qantis';
    private const string KEY_DESCRIPTION_WHITELABEL = 'description_whitelabel';
    private const string KEY_RSE_SCORE = 'rseScore';
    private const string KEY_BULLETPOINTS = 'bulletpoints';
    private const string KEY_DETAILS_TITLE = 'detailsTitle';
    private const string KEY_DETAILS_CONTENT = 'detailsContent';
    private const string KEY_ASSET_BUTTONS = 'assetButtons';
    private const string KEY_STEP_ITEMS = 'stepItems';
    private const string KEY_BUTTON_LABEL = 'buttonLabel';
    private const string KEY_ASSET_LINK = 'assetLink';
    private const string KEY_NEGOCIATED_TERMS_LAYER_ITEMS = 'negociatedTermsLayerItems';
    private const string KEY_STATUTS_RATTACHEMENT = 'statutsRattachement';
    private const string KEY_LAYER_MORE_INFORMATIONS = 'layerMoreInformations';
    private const string KEY_PHONE = 'phone';
    private const string KEY_PHONE_DESCRIPTION = 'phoneDescription';
    private const string KEY_NEGOCIATED_TERMS_BUTTON = 'negociatedTermsButton';
    private const string KEY_IMG_LINK = 'imgLink';
    private const string KEY_LABEL = 'label';
    private const string KEY_CONTACT_FORM = 'contactForm';

    public function __construct(
        private readonly StoryblokRichTextResolver $resolver,
        private readonly PhoneFormatter $phoneFormatter,
        private readonly ChannelContext $channelContext,
    ) {
    }

    /**
     * Convertit une réponse Storyblok en array de AccordCadre.
     */
    public function mapStories(array $storyblokResponse): array
    {
        return \array_map(
            fn (array $story) => $this->mapAccordCadre($story),
            $storyblokResponse[self::KEY_STORIES] ?? []
        );
    }

    /**
     * Convertit le contenu Storyblok en AccordCadre.
     */
    public function mapAccordCadre(array $contentData): AccordCadreContent
    {
        $content = new AccordCadreContent();
        $content->setTarifId($this->getContent($contentData[self::KEY_CONTENT][self::KEY_TARIF_ID] ?? null));
        $content->setLabelCtaRattachement($this->getContent($contentData[self::KEY_CONTENT][self::KEY_LABEL_CTA_RATTACHEMENT] ?? null));
        $content->setUrlCtaRattachement($this->getContent($contentData[self::KEY_CONTENT][self::KEY_URL_CTA_RATTACHEMENT] ?? null));
        $content->setName($this->getContent($contentData[self::KEY_CONTENT][self::KEY_ACCORD_CADRE_NAME] ?? null));
        $type = DjustAccordCadreType::tryFrom($contentData[self::KEY_CONTENT][self::KEY_ACCORD_CADRE_TYPE] ?? '');
        $content->setType($type?->value);
        $content->setLabelNotActivated($this->getContent($contentData[self::KEY_CONTENT][self::KEY_STATUTS_RATTACHEMENT][0][self::KEY_LABEL_NOT_ACTIVATED] ?? null));
        $content->setLabelPending($this->getContent($contentData[self::KEY_CONTENT][self::KEY_STATUTS_RATTACHEMENT][0][self::KEY_LABEL_PENDING] ?? null));
        $content->setLabelActivated($this->getContent($contentData[self::KEY_CONTENT][self::KEY_STATUTS_RATTACHEMENT][0][self::KEY_LABEL_ACTIVATED] ?? null));
        $content->setConfirmationLayerDescription($this->getTextAreaValue($contentData[self::KEY_CONTENT][self::KEY_CONFIRMATION_LAYER_DESCRIPTION] ?? null));
        $content->setConfirmationLayerSuccess($this->getTextAreaValue($contentData[self::KEY_CONTENT][self::KEY_SUCCESS_LAYER_DESCRIPTION] ?? null));
        $content->setContactForm((bool) ($contentData[self::KEY_CONTENT][self::KEY_CONTACT_FORM] ?? false));

        foreach ($contentData[self::KEY_CONTENT][self::KEY_BODY] ?? [] as $block) {
            $mappedBlock = match ($block[self::KEY_COMPONENT]) {
                AccordCadreBlock::BANNER->value => $this->mapBannerBlock($block),
                AccordCadreBlock::PRESENTATION->value => $this->mapPresentationBlock($block),
                AccordCadreBlock::NEGOCIATED_TERMS->value => $this->mapNegociatedTermsBlock($block),
                AccordCadreBlock::STEPS->value => $this->mapStepsBlock($block),
            };

            $content->addListBlock($mappedBlock);
        }

        return $content;
    }

    public function getContent(?string $contentData): ?string
    {
        return ($contentData === null || $contentData === '') ? null : $contentData;
    }

    private function getTextAreaValue(string|array|null $blockTextAreaEntry): ?string
    {
        if ($blockTextAreaEntry === null) {
            return null;
        }

        if (\is_string($blockTextAreaEntry)) {
            return $blockTextAreaEntry;
        }

        if (
            !isset($blockTextAreaEntry[self::KEY_CONTENT][0])
        ) {
            return null;
        }

        return $this->resolver->render($blockTextAreaEntry);
    }

    private function mapBannerBlock(array $block): BannerBlockContent
    {
        $mappedBannerBlock = new BannerBlockContent();
        $mappedBannerBlock
            ->setComponentName($block[self::KEY_COMPONENT])
            ->setImgBannerUrlDesktop($this->getContent($block[self::KEY_IMG_BANNER_URL_DESKTOP] ?? null))
            ->setImgBannerUrlMobile($this->getContent($block[self::KEY_IMG_BANNER_URL_MOBILE] ?? null))
            ->setLogoUrl($this->getContent($block[self::KEY_LOGO_URL] ?? null))
            ->setBadgeTextTop($this->getContent($block[self::KEY_BADGE_TEXT_TOP] ?? null))
            ->setBadgeTextBottom($this->getContent($block[self::KEY_BADGE_TEXT_BOTTOM] ?? null));

        return $mappedBannerBlock;
    }

    private function mapPresentationBlock(array $block): PresentationBlockContent
    {
        $mappedPresentationBlock = new PresentationBlockContent();
        $mappedPresentationBlock
            ->setComponentName($block[self::KEY_COMPONENT])
            ->setRseScore($this->getContent($block[self::KEY_RSE_SCORE] ?? null))
            ->setTitle($this->getContent($block[self::KEY_TITLE] ?? null))
            ->setBulletpoints($this->getTextAreaValue($block[self::KEY_BULLETPOINTS] ?? null))
            ->setDescription($this->getContent($block[self::KEY_DESCRIPTION] ?? null));

        $mappedPresentationBlock->setLayerMoreInformationsDescription($this->getTextAreaValue($block[self::KEY_LAYER_MORE_INFORMATIONS][0][self::KEY_DESCRIPTION] ?? null));
        $mappedPresentationBlock->setLayerMoreInformationsPhone($this->phoneFormatter->format($block[self::KEY_LAYER_MORE_INFORMATIONS][0][self::KEY_PHONE] ?? null));
        $mappedPresentationBlock->setLayerMoreInformationsPhoneDescription($this->getTextAreaValue($block[self::KEY_LAYER_MORE_INFORMATIONS][0][self::KEY_PHONE_DESCRIPTION] ?? null));

        foreach ($block[self::KEY_LAYER_MORE_INFORMATIONS][0][self::KEY_ASSET_BUTTONS] ?? [] as $assetButton) {
            $mappedAssetButton = new AssetButton();
            $mappedAssetButton
                ->setButtonLabel($this->getContent($assetButton[self::KEY_BUTTON_LABEL] ?? null))
                ->setAssetLink($this->getContent($assetButton[self::KEY_ASSET_LINK] ?? null));
            $mappedPresentationBlock->addLayerMoreInformationsAssetButton($mappedAssetButton);
        }

        return $mappedPresentationBlock;
    }

    private function mapNegociatedTermsBlock(array $block): NegociatedTermsBlockContent
    {
        $mappedNegociatedTermsBlock = new NegociatedTermsBlockContent();

        $mappedNegociatedTermsBlock
            ->setComponentName($block[self::KEY_COMPONENT])
            ->setTitle($this->getContent($block[self::KEY_TITLE] ?? null))
            ->setDescription($this->getTextAreaValue($block[self::KEY_DESCRIPTION] ?? null))
            ->setDetailsTitle($this->getContent($block[self::KEY_DETAILS_TITLE] ?? null))
            ->setDetailsContent($this->getTextAreaValue($block[self::KEY_DETAILS_CONTENT] ?? null));

        $negociatedTermsButton = $block[self::KEY_NEGOCIATED_TERMS_BUTTON][0] ?? null;

        if ($negociatedTermsButton) {
            $mappedNegociatedTermsBlock->setNegociatedTermsButtonLabel(
                $this->getContent($negociatedTermsButton[self::KEY_LABEL] ?? '')
            );
            foreach ($negociatedTermsButton[self::KEY_NEGOCIATED_TERMS_LAYER_ITEMS] ?? [] as $imageItem) {
                $mappedImageItem = new ImageItem();
                $mappedImageItem
                    ->setImgLink($this->getContent($imageItem[self::KEY_IMG_LINK] ?? null));
                $mappedNegociatedTermsBlock->addNegociatedTermsLayerItem($mappedImageItem);
            }
        }

        foreach ($block[self::KEY_ASSET_BUTTONS] ?? [] as $assetButton) {
            $imageItem = new AssetButton();
            $imageItem
                ->setButtonLabel($this->getContent($assetButton[self::KEY_BUTTON_LABEL] ?? null))
                ->setAssetLink($this->getContent($assetButton[self::KEY_ASSET_LINK] ?? null));
            $mappedNegociatedTermsBlock->addAssetButton($imageItem);
        }

        return $mappedNegociatedTermsBlock;
    }

    private function mapStepsBlock(array $block): StepsBlockContent
    {
        $channel = $this->channelContext->getChannel();
        $isWhiteLabel = $channel->getChannelParameter()->isWhiteLabel();

        $mappedStepsBlock = new StepsBlockContent();
        $mappedStepsBlock
            ->setComponentName($block[self::KEY_COMPONENT])
            ->setTitle($this->getContent($block[self::KEY_TITLE] ?? null));

        foreach ($block[self::KEY_STEP_ITEMS] ?? [] as $stepsItem) {
            $mappedStepsItem = new StepItem();

            $title = $this->getStepItemContent(
                $stepsItem,
                $isWhiteLabel,
                self::KEY_TITLE_WHITELABEL,
                self::KEY_TITLE_QANTIS
            );

            $description = $this->getStepItemContent(
                $stepsItem,
                $isWhiteLabel,
                self::KEY_DESCRIPTION_WHITELABEL,
                self::KEY_DESCRIPTION_QANTIS
            );

            $mappedStepsItem
                ->setTitle($this->getContent($title))
                ->setDescription($this->getContent($description));

            $mappedStepsBlock->addStepItem($mappedStepsItem);
        }

        return $mappedStepsBlock;
    }

    private function getStepItemContent(
        array $stepsItem,
        bool $isWhiteLabel,
        string $whiteLabelKey,
        string $qantisKey
    ): string {
        if ($isWhiteLabel && isset($stepsItem[$whiteLabelKey])) {
            return $stepsItem[$whiteLabelKey];
        }

        return $stepsItem[$qantisKey] ?? '';
    }
}
