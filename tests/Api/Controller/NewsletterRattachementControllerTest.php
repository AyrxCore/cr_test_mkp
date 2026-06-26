<?php

declare(strict_types=1);

use App\DataFixtures\Factory\PartnerFactory;
use App\Service\AccordCadreSubscriptionService;
use App\Service\MailerProvider;
use Symfony\Component\HttpFoundation\Response;

uses()->group('ApiNewsletterRattachementController');

\beforeEach(function () {
    $this->client = $this->createClientWithCredentials();
    $this->cache = $this->client->getContainer()->get('cache.app');
    $this->mailerProvider = Mockery::mock(MailerProvider::class);
    $this->subscriptionServiceMock = Mockery::mock(AccordCadreSubscriptionService::class);
    $this->partner = PartnerFactory::createOne([
        'rattachementRecipients' => ['email_partner@mail.com'],
    ]);
    $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
});

\it('loads loading page', function () {
    $response = $this->client->request('GET', '/rattachement-newsletter', [
        'query' => [
            'email' => 'valid@email.com',
            'partnerId' => (string) $this->partner->getId(),
        ]]);

    \expect($response->getStatusCode())->toBe(Response::HTTP_OK);
    \expect($response->getContent())->toContain('Traitement en cours');
});

\it('shows error if email is invalid', function () {
    $email = 'email-invalide';
    $response = $this->client->request('POST', '/rattachement-newsletter/process', [
        'query' => [
            'email' => $email,
            'partnerId' => 1,
        ]]);

    \expect($response->getStatusCode())->toBe(Response::HTTP_OK);
    \expect($response->getContent())->toContain('L&#039;adresse email '.$email.'est invalide');
});

\it('shows error if partner does not exist', function () {
    $response = $this->client->request('POST', '/rattachement-newsletter/process', [
        'query' => [
            'email' => 'valid@email.com',
            'partnerId' => 99999,
        ]]);

    \expect($response->getStatusCode())->toBe(Response::HTTP_OK);
    \expect($response->getContent())->toContain('Fournisseur non identifié');
});

\it('shows error if no account found for email', function () {
    $email = 'unknown@email.com';

    $response = $this->client->request('POST', '/rattachement-newsletter/process', [
        'query' => [
            'email' => $email,
            'partnerId' => (string) $this->partner->getId(),
        ]]);

    \expect($response->getStatusCode())->toBe(Response::HTTP_OK);
    \expect($response->getContent())->toContain('Aucun compte n&#039;a été trouvé pour le mail '.$email);
});

\it('catch cache data and skip sending partner email', function () {
    $partnerId = (string) $this->partner->getId();
    $email = 'test@qantis.co';
    $host = 'localhost';

    $cacheItem = $this->cache->getItem('allow_unique_link');
    $cacheItem->set(['partnerId' => $partnerId, 'email' => $email, 'host' => $host]);
    $this->cache->save($cacheItem);

    $this->subscriptionServiceMock->shouldReceive('subscription')
        ->andReturn(true);

    $response = $this->client->request('POST', '/rattachement-newsletter/process', [
        'query' => [
            'email' => $email,
            'partnerId' => $partnerId,
        ]]);

    \expect($response->getStatusCode())->toBe(Response::HTTP_FOUND);
});

\it('not sending email if rattachement recipients for partner is null', function () {
    $partnerId = (string) $this->partner->getId();
    $this->partner->setRattachementRecipients(null);
    $this->entityManager->persist($this->partner);
    $this->entityManager->flush();
    $email = 'test@qantis.co';

    $this->subscriptionServiceMock->shouldReceive('subscription')
        ->andReturn(true);

    $response = $this->client->request('POST', '/rattachement-newsletter/process', [
        'query' => [
            'email' => $email,
            'partnerId' => $partnerId,
        ]]);

    \expect($response->getStatusCode())->toBe(Response::HTTP_FOUND);

    $this->assertEmailCount(0);

    $email = $this->getMailerMessage();
    \expect($email)->toBeNull();
});

\it('successfully processes subscription when account and partner exist', function () {
    $this->cache->clear();

    $response = $this->client->request('POST', '/rattachement-newsletter/process', [
        'query' => [
            'email' => 'test@qantis.co',
            'partnerId' => (string) $this->partner->getId(),
        ]]);

    \expect($response->getStatusCode())->toBe(Response::HTTP_FOUND);

    $this->assertEmailCount(1);

    $email = $this->getMailerMessage();
    \expect($email)->not->toBeNull();
    \expect($email->getSubject())->toContain('Demande de rattachement');
});
