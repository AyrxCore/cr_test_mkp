<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class UpplerOrderService extends AbstractUpplerService
{
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    public function getOrdersByUserId(int $userId): array|null
    {
        try {
            $res = $this->request(
                'GET',
                'v1/buyer/order/?criteria[user]='.$userId.'&expand[]=buyer_user&criteria[type]=order',
            );
        } catch (\Exception $exception) {
            $this->apiLogger->error('Erreur :'.$exception->getMessage());
            throw new \Exception('Une erreur est survenue lors de la recherche des commande, veuillez contacter les administrateurs du site');
        }

        if ($res->getStatusCode() !== Response::HTTP_OK) {
            throw new NotFoundHttpException("Aucune commande n'a pas été trouvée");
        }

        return \json_decode($res->getContent(), true);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    public function getOrderByIdAndUserId(int $orderId, int $userId): \stdClass
    {
        $res = $this->request(
            'GET',
            'v1/buyer/order/'.$orderId.'?expand[]=items&expand[]=shipments',
        );

        if ($res->getStatusCode() !== Response::HTTP_OK) {
            $this->apiLogger->error($res->getStatusCode()." La commande avec l'ID: ".$orderId.' du User '.$userId." n'a pas été trouvée");

            throw new NotFoundHttpException("La commande avec l'ID ".$orderId." n'a pas été trouvée");
        }

        $remoteOrder = \json_decode($res->getContent(), true);

        if ($userId !== $remoteOrder->buyer_user->id) {
            throw new AccessDeniedException("Vous n'avez pas de commande avec cet identifiant");
        }

        return $remoteOrder;
    }

    public function getOrderInvoiceByIdAndUserId(int $orderInvoiceId): array
    {
        $res = $this->request(
            'GET',
            'v1/buyer/payment/'.$orderInvoiceId.'/download',
        );

        if ($res->getStatusCode() !== Response::HTTP_OK) {
            $this->apiLogger->error($res->getStatusCode()." La facture avec l'ID:  $orderInvoiceId  n'a pas été trouvée");

            throw new FileNotFoundException(" La facture avec l'ID:  $orderInvoiceId  n'a pas été trouvée");
        }

        return ['headers' => $res->getHeaders(), 'content' => \bin2hex($res->getContent())];
    }
}
