<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\SubAccount;
use App\Entity\Account;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

class UpplerAccountService extends HttpClientProvider
{
    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;

    public function getUserSubAccountDatas(): object | null
    {
        $session = $this->requestStack->getSession();
        /**@var Account $account*/
        $account = $session->get('account');
        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/administrator/sub-account/' .
            $account->getUpplerSubAccountId()  .
            '?expand[]=accounter',
            [],
            true
        );
        if (Response::HTTP_OK === $res->getStatusCode()) {
            $account = $this->computeSubAccount(json_decode($res->getContent()));
            return $account;
        }

        return null;
    }

    public function updateUserSubAccountDatas(SubAccount $subAccount): bool
    {
        $datas = [];
        if (null !== $subAccount->getBillingAddressId()) {
            $datas["billing_address_id"] = $subAccount->getBillingAddressId();
        }

        if (null !== $subAccount->getShippingAddressId()) {
            $datas["shipping_address_id"] = $subAccount->getShippingAddressId();
        }

        if (null !== $subAccount->getEmail()) {
            $datas["email"] = $subAccount->getEmail();
        }

        if (null !== $subAccount->getLastName()) {
            $datas["lastname"] = $subAccount->getLastName();
        }

        if (null !== $subAccount->getFirstName()) {
            $datas["firstname"] = $subAccount->getFirstName();
        }

            // TODO Uppler n'accepte pas ce champ en PATCH, voir avec eux pour faire évoluer cela
//        if (null !== $subAccount->getPhone()) {
//            $datas["phone"] = $subAccount->getPhone();
//        }

        $res = $this->request(
            'PATCH',
            $this->apiUrl . 'v1/administrator/sub-account/' . $subAccount->getId(),
            [
                'json' => $datas
            ],
            true
        );

        if (Response::HTTP_NO_CONTENT === $res->getStatusCode()) {
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
            null !== $subAccount->shipping_address &&
            property_exists($subAccount->shipping_address, 'id')
        )
            ? $subAccount->shipping_address->id
            : $subAccount->shipping_address
        ;
        $account->billing_address = (
            null !== $subAccount->billing_address &&
            property_exists($subAccount->billing_address, 'id')
        )
            ? $subAccount->billing_address->id
            : $subAccount->billing_address
        ;
        return $account;
    }

}
