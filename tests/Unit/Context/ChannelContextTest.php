<?php

declare(strict_types=1);

use App\Context\ChannelContext;
use App\Entity\Channel;
use App\Repository\ChannelRepository;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

\afterEach(function () {
    Mockery::close();
});

\it('fails when trying to get a channel without request', function () {
    $channelRepository = Mockery::mock(ChannelRepository::class);

    $requestStack = Mockery::mock(RequestStack::class);
    $requestStack->shouldReceive('getMainRequest')->andReturnNull();

    $channelContext = new ChannelContext($requestStack, $channelRepository);

    \expect(function () use ($channelContext) {
        $channelContext->getChannel();
    })->toThrow(RuntimeException::class);
})->group('channelContext');

\it('fails when trying to get a channel without X-Channel header', function () {
    $channelRepository = Mockery::mock(ChannelRepository::class);

    $headerBag = Mockery::mock(HeaderBag::class);
    $headerBag->shouldReceive('get')->with('X-Channel')->andReturnNull();

    $request = Mockery::mock(Request::class);
    $request->headers = $headerBag;

    $requestStack = Mockery::mock(RequestStack::class);
    $requestStack->shouldReceive('getMainRequest')->andReturn($request);

    $channelContext = new ChannelContext($requestStack, $channelRepository);

    \expect(function () use ($channelContext) {
        $channelContext->getChannel();
    })->toThrow(BadRequestHttpException::class);
})->group('channelContext');

\it('fails when trying to get a non-existing channel', function () {
    $fakeChannelCode = 'FAKE_CODE';

    $channelRepository = Mockery::mock(ChannelRepository::class);
    $channelRepository->shouldReceive('findOneByCode')->with($fakeChannelCode)->andReturnNull();

    $headerBag = Mockery::mock(HeaderBag::class);
    $headerBag->shouldReceive('get')->with('X-Channel')->andReturn($fakeChannelCode);

    $request = Mockery::mock(Request::class);
    $request->headers = $headerBag;

    $requestStack = Mockery::mock(RequestStack::class);
    $requestStack->shouldReceive('getMainRequest')->andReturn($request);

    $channelContext = new ChannelContext($requestStack, $channelRepository);

    \expect(function () use ($channelContext) {
        $channelContext->getChannel();
    })->toThrow(BadRequestHttpException::class);
})->group('channelContext');

\it('gets an existing channel', function () {
    $fakeChannelCode = 'FAKE_CODE';

    $fakeChannel = Mockery::mock(Channel::class);

    $channelRepository = Mockery::mock(ChannelRepository::class);
    $channelRepository->shouldReceive('findOneByCode')->with($fakeChannelCode)->andReturn($fakeChannel);

    $headerBag = Mockery::mock(HeaderBag::class);
    $headerBag->shouldReceive('get')->with('X-Channel')->andReturn($fakeChannelCode);

    $request = Mockery::mock(Request::class);
    $request->headers = $headerBag;

    $requestStack = Mockery::mock(RequestStack::class);
    $requestStack->shouldReceive('getMainRequest')->andReturn($request);

    $channelContext = new ChannelContext($requestStack, $channelRepository);

    \expect($channelContext->getChannel())->toBe($fakeChannel);
})->group('channelContext');
