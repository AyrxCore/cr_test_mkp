<?php

declare(strict_types=1);

use App\DataFixtures\Factory\AccountFactory;
use App\DataFixtures\Factory\UserFactory;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;

\beforeEach(function () {
    $this->client = $this->createClientWithCredentials();
    $this->faker = Factory::create();

    $this->adherentId = $this->faker->uuid;
    $this->data = [
        'email' => $this->faker->email,
        'adherentId' => $this->adherentId,
        'adherentName' => $this->faker->name,
        'adherentParentId' => $this->faker->uuid,
        'firstname' => $this->faker->firstName,
        'lastname' => $this->faker->lastName,
        'phone' => $this->faker->phoneNumber,
        'serviceFonction' => $this->faker->word,
        'isEnabled' => $this->faker->boolean,
        'channelCode' => $this->faker->word,
        'contactId' => $this->faker->uuid,
        'djustCustomerAccountId' => $this->faker->word,
        'djustCustomerUserId' => $this->faker->word,
        'djustUsername' => null,
        'djustPassword' => $this->faker->word,
        'upplerSubAccountId' => $this->faker->numberBetween(1, 1000),
        'upplerUserId' => $this->faker->numberBetween(1, 1000),
        'upplerCompanyId' => $this->faker->numberBetween(1, 1000),
        'upplerSubAccountClientId' => $this->faker->word,
        'upplerSubAccountClientSecret' => $this->faker->word,
    ];
});

\it('save new adherent, user and account from NEO payload', function () {
    $this->client->request('POST', '/api/user_accounts', [
        'json' => $this->data,
    ]);
    $this->assertResponseStatusCodeSame(201);
})->group('ApiUserAccountProcessorTest');

\it('save new adherent from NEO payload with existant user and account', function () {
    $email = $this->faker->email;
    $contactId = Uuid::v4();
    $newPhone = $this->faker->phoneNumber;
    $this->data['email'] = $email;
    $this->data['contactId'] = (string) $contactId;
    $this->data['phone'] = $newPhone;

    $user = UserFactory::createOne(['email' => $email]);
    AccountFactory::createOne(['contactId' => $contactId, 'user' => $user, 'phone' => '+33612345678']);

    $this->client->request('POST', '/api/user_accounts', [
        'json' => $this->data,
    ]);

    $this->assertResponseStatusCodeSame(201);
})->group('ApiUserAccountProcessorTest');

\it('throw error 400 if wrong type for datas', function ($field, $value) {
    $this->data[$field] = $value;
    $this->client->request('POST', '/api/user_accounts', [
        'json' => $this->data,
    ]);
    $this->assertResponseStatusCodeSame(400);
})->with([
    'wrong email type' => [
        'email',
        1234,
    ],
    'wrong adherentId type' => [
        'adherentId',
        1234,
    ],
    'wrong adherentName type' => [
        'adherentName',
        1234,
    ],
    'wrong adherentParentId type' => [
        'adherentParentId',
        1234,
    ],
    'wrong firstname type' => [
        'firstname',
        1234,
    ],
    'wrong lastname type' => [
        'lastname',
        1234,
    ],
    'wrong phone type' => [
        'phone',
        1234,
    ],
    'wrong serviceFonction type' => [
        'serviceFonction',
        1234,
    ],
    'wrong isEnabled type' => [
        'isEnabled',
        'test',
    ],
    'wrong channelCode type' => [
        'channelCode',
        1234,
    ],
    'wrong contactId type' => [
        'contactId',
        1234,
    ],
    'wrong djustCustomerAccountId type' => [
        'djustCustomerAccountId',
        1234,
    ],
    'wrong djustCustomerUserId type' => [
        'djustCustomerUserId',
        1234,
    ],
    'wrong djustUsername type' => [
        'djustUsername',
        1234,
    ],
    'wrong djustPassword type' => [
        'djustPassword',
        1234,
    ],
    'wrong upplerSubAccountId type' => [
        'upplerSubAccountId',
        'test',
    ],
    'wrong upplerUserId type' => [
        'upplerUserId',
        'test',
    ],
    'wrong upplerCompanyId type' => [
        'upplerCompanyId',
        'test',
    ],
    'wrong upplerSubAccountClientId type' => [
        'upplerSubAccountClientId',
        1234,
    ],
    'wrong upplerSubAccountClientSecret type' => [
        'upplerSubAccountClientSecret',
        1234,
    ],
])->group('ApiUserAccountProcessorTest');
