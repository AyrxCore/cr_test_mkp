<?php

declare(strict_types=1);

use App\DataFixtures\Factory\UserFactory;
use App\DataFixtures\Story\UserStory;
use App\Tests\Story\Authentication\AuthenticationStory;
use Symfony\Component\HttpFoundation\Cookie;

\it('authenticates a user', function (
    $username,
    $password,
    $expectedStatusCode,
    $expectedRoles,
    $expectedResponse = null,
    $channel = 'QANTIS_ACHAT',
) {
    // load dev fixtures
    UserStory::load();

    $client = $this::createClient(defaultOptions: [
        'headers' => [
            'X-Channel' => $channel,
        ],
    ]);

    $response = $client->request('POST', '/api/authentication/token', [
        'json' => [
            'username' => $username,
            'password' => $password,
        ],
    ]);

    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame($expectedStatusCode);
    $this->assertResponseHasCookie('BEARER');

    $cookie = Cookie::fromString($response->getHeaders()['set-cookie'][0]);

    [, $jwtPayloadPart] = \explode('.', $cookie->getValue());
    $decodedJwt = \json_decode(\base64_decode($jwtPayloadPart, true), true);

    \expect($decodedJwt)
        ->toMatchArray([
            'username' => $username,
            'roles' => $expectedRoles,
        ]);

    if (!\in_array('ROLE_API', $expectedRoles, true)) {
        $user = UserFactory::find(['username' => $username])->object();
        $account = $this::getUserFirstAccount($user, $channel);
        $this->assertNotNull($account);
        // Select an account to initialize the session
        $client->request('GET', "/api/accounts/{$account->getId()}/select");
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        // get current user info
        $client->request('GET', '/api/me');
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonResponseMatches($expectedResponse);
    }
})
    ->with([
        'user with ROLE_USER' => [
            'username' => 'gsm@qantis.co',
            'password' => '23AP4DF8',
            'expectedStatusCode' => 204,
            'roles' => ['ROLE_USER'],
            'expectedResponse' => 'me/response_user_with_marketplace_access.json',
        ],
        'user with ROLE_API' => [
            'username' => 'api_user',
            'password' => '23AP4DF8',
            'expectedStatusCode' => 204,
            'roles' => ['ROLE_API', 'ROLE_USER'],
        ],
    ])
    ->group('authentication');

\it('fails to authenticate a user', function (
    $username,
    $password,
    $expectedErrorMessage,
) {
    AuthenticationStory::load();

    $this::createClient()->request('POST', '/api/authentication/token', [
        'json' => [
            'username' => $username,
            'password' => $password,
        ],
    ]);

    $this->assertResponseStatusCodeSame(401);
    $this->assertJsonEquals(['code' => 401, 'message' => $expectedErrorMessage]);
})
    ->with([
        'unknown user' => [
            'username' => 'unknown_username',
            'password' => 'some_password',
            'error_message' => 'Identifiants invalides.',
        ],
        'wrong password' => [
            'username' => 'gsm@qantis.co',
            'password' => 'wrong_password',
            'error_message' => 'Identifiants invalides.',
        ],
        'ROLE_API + user disabled' => [
            'username' => 'test_role_api_user_disabled',
            'password' => '000000',
            'error_message' => 'user_disabled',
        ],
        'user with disabled account' => [
            'username' => 'test_user_with_disabled_account',
            'password' => '000000',
            'error_message' => 'user_empty_account',
        ],
        'user without account' => [
            'username' => 'test_user_without_account',
            'password' => '000000',
            'error_message' => 'user_empty_account',
        ],
    ])
    ->group('authentication');
