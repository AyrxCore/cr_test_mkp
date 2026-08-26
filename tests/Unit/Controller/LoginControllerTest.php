<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\LoginController;
use App\Repository\AccountRepository;
use App\Repository\ChannelRepository;
use App\Repository\UserRepository;
use App\Service\LogAutoLoginErrorService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

\uses()->group('UnitLoginController');

\beforeEach(function () {
    $this->logger = \Mockery::mock(LoggerInterface::class);
    $this->requestStack = \Mockery::mock(RequestStack::class);
    $this->channelRepository = \Mockery::mock(ChannelRepository::class);

    $this->controller = new LoginController(
        \Mockery::mock(EventDispatcherInterface::class),
        $this->requestStack,
        \Mockery::mock(UserPasswordHasherInterface::class),
        \Mockery::mock(UserRepository::class),
        \Mockery::mock(AccountRepository::class),
        \Mockery::mock(LogAutoLoginErrorService::class),
        $this->logger,
    );

    $this->controller->setContainer(static::getContainer());
    $this->controller->setChannelRepository($this->channelRepository);
});

\afterEach(function () {
    \Mockery::close();
});

\it('logs a warning and redirects when POST payload is missing _username', function () {
    $session = \Mockery::mock(SessionInterface::class);
    $this->requestStack->shouldReceive('getSession')->andReturn($session);
    $this->channelRepository->shouldReceive('findOneBy')->andReturn(null);

    $this->logger
        ->shouldReceive('warning')
        ->once()
        ->with(
            'Login request received without _username in payload.',
            \Mockery::on(fn (array $context) => $context['route'] === 'reset_password' && \array_key_exists('ip', $context))
        );

    $request = Request::create('/login/reset-password', 'POST', []);
    $request->attributes->set('_route', 'reset_password');

    $response = $this->controller->request(
        $request,
        \Mockery::mock(EntityManagerInterface::class),
        \Mockery::mock(TranslatorInterface::class),
    );

    expect($response)->toBeInstanceOf(RedirectResponse::class);
});

\it('logs a warning and redirects when POST payload has an empty _username', function () {
    $session = \Mockery::mock(SessionInterface::class);
    $this->requestStack->shouldReceive('getSession')->andReturn($session);
    $this->channelRepository->shouldReceive('findOneBy')->andReturn(null);

    $this->logger
        ->shouldReceive('warning')
        ->once()
        ->with(
            'Login request received without _username in payload.',
            \Mockery::on(fn (array $context) => $context['route'] === 'first_signin' && \array_key_exists('ip', $context))
        );

    $request = Request::create('/login/first-signin', 'POST', ['_username' => '']);
    $request->attributes->set('_route', 'first_signin');

    $response = $this->controller->request(
        $request,
        \Mockery::mock(EntityManagerInterface::class),
        \Mockery::mock(TranslatorInterface::class),
    );

    expect($response)->toBeInstanceOf(RedirectResponse::class);
});

\it('logs a warning and redirects when POST payload has a non-string _username', function () {
    $session = \Mockery::mock(SessionInterface::class);
    $this->requestStack->shouldReceive('getSession')->andReturn($session);
    $this->channelRepository->shouldReceive('findOneBy')->andReturn(null);

    $this->logger
        ->shouldReceive('warning')
        ->once()
        ->with(
            'Login request received without _username in payload.',
            \Mockery::on(fn (array $context) => $context['route'] === 'first_signin' && \array_key_exists('ip', $context))
        );

    $request = Request::create('/login/first-signin', 'POST', ['_username' => ['injected', 'array']]);
    $request->attributes->set('_route', 'first_signin');

    $response = $this->controller->request(
        $request,
        \Mockery::mock(EntityManagerInterface::class),
        \Mockery::mock(TranslatorInterface::class),
    );

    expect($response)->toBeInstanceOf(RedirectResponse::class);
});
