<?php

declare(strict_types=1);

use App\Context\ChannelContext;
use App\Dto\AccordCadre\AccordCadreContent;
use App\Entity\Channel;
use App\Entity\ChannelParameter;
use App\Enum\Storyblok\AccordCadreBlock;
use App\Helper\Formatter\PhoneFormatter;
use App\Service\AccordCadre\StoryblokToAccordCadreMapper;
use App\Service\Storyblok\StoryblokRichTextResolver;
use App\Tests\Unit\Helper\JsonHelper;

\beforeEach(function () {
    $resolver = new StoryblokRichTextResolver();
    $phoneFormatter = new PhoneFormatter();
    $this->channelContext = Mockery::mock(ChannelContext::class);

    $this->mapper = new StoryblokToAccordCadreMapper($resolver, $phoneFormatter, $this->channelContext);
});

\it('maps a complete story with all fields', function () {
    $storyblokData = JsonHelper::getJsonDataFile('_mocks/storyblok/accord-cadre-response.json');
    $channel = Mockery::mock(Channel::class);
    $channelParameter = Mockery::mock(ChannelParameter::class);
    $this->channelContext->shouldReceive('getChannel')->once()->andReturn($channel);
    $channel->shouldReceive('getChannelParameter')->once()->andReturn($channelParameter);
    $channelParameter->shouldReceive('isWhiteLabel')->once()->andReturn(false);
    $result = $this->mapper->mapAccordCadre($storyblokData);

    \expect($result)->toBeInstanceOf(AccordCadreContent::class)
        ->and($result->getTarifId())->toBe('67a1dc3f-564c-2485-515c-54b7f3ee9bf5')
        ->and($result->getLabelCtaRattachement())->toBe('Activer mes avantages');

    $bannerBlock = $result->getListBlocks()[AccordCadreBlock::BANNER->value];
    \expect($bannerBlock->getComponentName())->toBe('bannerBlock')
        ->and($bannerBlock->getLogoUrl())->toBe('https://ged-wp-files.s3.amazonaws.com/marketplace/Accords-cadres/PEUGEOT/PEUGEOT-Logo-Marketplace.jpg')
        ->and($bannerBlock->getImgBannerUrlDesktop())->toBe('https://ged-wp-files.s3.amazonaws.com/marketplace/Accords-cadres/PEUGEOT/Banniere-Peugeot-new.jpg')
        ->and($bannerBlock->getImgBannerUrlMobile())->toBe('https://ged-wp-files.s3.amazonaws.com/marketplace/Accords-cadres/PEUGEOT/Banniere-Peugeot-new-mobile.jpg')
        ->and($bannerBlock->getBadgeTextBottom())->toBe('-70%')
        ->and($bannerBlock->getBadgeTextTop())->toBe('Jusqu\'à');

    $presentationBlock = $result->getListBlocks()[AccordCadreBlock::PRESENTATION->value];
    \expect($presentationBlock->getComponentName())->toBe('presentationBlock')
        ->and($presentationBlock->getRseScore())->toBe('8,4')
        ->and($presentationBlock->getTitle())->toContain('Peugeot')
        ->and($presentationBlock->getDescription())->toContain('Peugeot')
        ->and($presentationBlock->getLayerMoreInformationsDescription())->toContain('Test de description du layer')
        ->and($presentationBlock->getLayerMoreInformationsPhone())->toBe('06 35 46 35 46')
        ->and($presentationBlock->getLayerMoreInformationsPhoneDescription())->toContain('Test de description du téléphone');

    $negociatedTermsBlock = $result->getListBlocks()[AccordCadreBlock::NEGOCIATED_TERMS->value];
    \expect($negociatedTermsBlock->getComponentName())->toBe('negociatedTermsBlock')
        ->and($negociatedTermsBlock->getTitle())->toBe('Conditions négociées')
        ->and($negociatedTermsBlock->getDescription())->toContain('Test de description des conditions négociées')
        ->and($negociatedTermsBlock->getDetailsTitle())->toBe('Détails et engagements')
        ->and($negociatedTermsBlock->getDetailsContent())->toContain('Test du contenu')
        ->and($negociatedTermsBlock->getDetailsContent())->toContain('Détails et engagements')
        ->and($negociatedTermsBlock->getAssetButtons())->toHaveCount(2);

    $stepsBlock = $result->getListBlocks()[AccordCadreBlock::STEPS->value];
    \expect($stepsBlock->getComponentName())->toBe('stepsBlock')
        ->and($stepsBlock->getTitle())->toBe('Comment en bénéficier')
        ->and($stepsBlock->getStepItems())->toHaveCount(3);
})->group('StoryblokToAccordCadreMapper', 'storyblok');

\it('maps contactForm as false when absent from storyblok data', function () {
    $storyblokData = JsonHelper::getJsonDataFile('_mocks/storyblok/accord-cadre-response.json');
    $channel = Mockery::mock(Channel::class);
    $channelParameter = Mockery::mock(ChannelParameter::class);
    $this->channelContext->shouldReceive('getChannel')->once()->andReturn($channel);
    $channel->shouldReceive('getChannelParameter')->once()->andReturn($channelParameter);
    $channelParameter->shouldReceive('isWhiteLabel')->once()->andReturn(false);

    $result = $this->mapper->mapAccordCadre($storyblokData);

    \expect($result->isContactForm())->toBeFalse();
})->group('StoryblokToAccordCadreMapper', 'storyblok');

\it('ignores unknown blocks instead of throwing', function () {
    $storyblokData = JsonHelper::getJsonDataFile('_mocks/storyblok/accord-cadre-response.json');
    $storyblokData['content']['body'][] = ['component' => 'unknownFutureBlock'];
    $storyblokData['content']['body'][] = ['title' => 'block without component'];
    $channel = Mockery::mock(Channel::class);
    $channelParameter = Mockery::mock(ChannelParameter::class);
    $this->channelContext->shouldReceive('getChannel')->once()->andReturn($channel);
    $channel->shouldReceive('getChannelParameter')->once()->andReturn($channelParameter);
    $channelParameter->shouldReceive('isWhiteLabel')->once()->andReturn(false);

    $result = $this->mapper->mapAccordCadre($storyblokData);

    \expect($result->getListBlocks())->not->toHaveKey('unknownFutureBlock');
})->group('StoryblokToAccordCadreMapper', 'storyblok');

\it('maps contactForm as true when set in storyblok data', function () {
    $storyblokData = JsonHelper::getJsonDataFile('_mocks/storyblok/accord-cadre-response.json');
    $storyblokData['content']['contactForm'] = true;
    $channel = Mockery::mock(Channel::class);
    $channelParameter = Mockery::mock(ChannelParameter::class);
    $this->channelContext->shouldReceive('getChannel')->once()->andReturn($channel);
    $channel->shouldReceive('getChannelParameter')->once()->andReturn($channelParameter);
    $channelParameter->shouldReceive('isWhiteLabel')->once()->andReturn(false);

    $result = $this->mapper->mapAccordCadre($storyblokData);

    \expect($result->isContactForm())->toBeTrue();
})->group('StoryblokToAccordCadreMapper', 'storyblok');

