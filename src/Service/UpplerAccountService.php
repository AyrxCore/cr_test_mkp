<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\SubAccount;
use App\Entity\Account;
use Symfony\Component\HttpFoundation\Response;

class UpplerAccountService extends AbstractUpplerService
{
    public function getUserSubAccountData(): object|null
    {
        $session = $this->requestStack->getSession();
        /** @var Account $account */
        $account = $session->get('account');

        $res = $this->request(
            'GET',
            'v1/administrator/sub-account/'.$account->getUpplerSubAccountId(),
            [
                'query' => [
                    'expand[]' => 'accounter',
                ],
            ],
            isAdmin: true
        );
        if ($res->getStatusCode() === Response::HTTP_OK) {
            $account = $this->computeSubAccount(\json_decode($res->getContent()));

            return $account;
        }

        return null;
    }

    public function updateUserSubAccountData(SubAccount $subAccount): bool
    {
        $data = [];
        if ($subAccount->getBillingAddressId() !== null) {
            $data['billing_address_id'] = $subAccount->getBillingAddressId();
        }

        if ($subAccount->getShippingAddressId() !== null) {
            $data['shipping_address_id'] = $subAccount->getShippingAddressId();
        }

        if ($subAccount->getEmail() !== null) {
            $data['email'] = $subAccount->getEmail();
        }

        if ($subAccount->getLastName() !== null) {
            $data['lastname'] = $subAccount->getLastName();
        }

        if ($subAccount->getFirstName() !== null) {
            $data['firstname'] = $subAccount->getFirstName();
        }

        $res = $this->request(
            'PATCH',
            'v1/administrator/sub-account/'.$subAccount->getId(),
            [
                'json' => $data,
            ],
            isAdmin: true
        );

        if ($res->getStatusCode() === Response::HTTP_NO_CONTENT) {
            return true;
        }

        return false;
    }

    private function computeSubAccount($subAccount)
    {
        $account = new \stdClass();
        $account->id = $subAccount->id;
        $account->email = $subAccount->accounter->email;
        $account->lastname = $subAccount->accounter->lastname;
        $account->firstname = $subAccount->accounter->firstname;

        $account->shipping_address = (
            $subAccount->shipping_address !== null
            && \property_exists($subAccount->shipping_address, 'id')
        )
            ? $subAccount->shipping_address->id
            : $subAccount->shipping_address;
        $account->billing_address = (
            $subAccount->billing_address !== null
            && \property_exists($subAccount->billing_address, 'id')
        )
            ? $subAccount->billing_address->id
            : $subAccount->billing_address;

        return $account;
    }
}
