<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\Order;
use App\Helper\UpplerHelper;

class OrderFactory extends AbstractFactory
{
    public function create(mixed $data): Order
    {
        $order = new Order();
        $order->setId($data['id']);
        $order->setOrderNumber(UpplerHelper::getOrderNumber($data));
        $order->setTotal(UpplerHelper::formatPrice($data['total']));
        $order->setTotalExcludingTaxes(UpplerHelper::formatPrice($data['total_excluding_taxes']));
        $order->setState($data['state']);

        $shippingAddress = $data['shipping_address'];
        $country = $shippingAddress['country']['name']['default'];
        $shippingAddress = \sprintf('%s %s %s, %s', $shippingAddress['street'], $shippingAddress['postcode'], $shippingAddress['city'], $country);
        $order->setShippingAddress($shippingAddress);
        $order->setShippingState($data['shipping_state']);
        $order->setCreatedAt(new \DateTime($data['created_at']));
        $order->setUpdatedAt(new \DateTime($data['updated_at']));
        $order->setItems($data['items']);

        $billingAddress = $data['billing_address'];
        $country = $billingAddress['country']['name']['default'];
        $billingAddress = \sprintf('%s %s %s, %s', $billingAddress['street'], $billingAddress['postcode'], $billingAddress['city'], $country);
        $order->setBillingAddress($billingAddress);
        if ($data['state'] === \strtolower(Order::ORDER_CONFIRMED)) {
            $order->setPaymentId($data['payment']['id']);
        }

        if ($data['shipped_at'] !== null) {
            $order->setShippedAt(new \DateTime($data['shipped_at']));
        }

        if ($data['confirmed_at'] !== null) {
            $order->setConfirmedAt(new \DateTime($data['confirmed_at']));
        }

        if ($data['delivered_at'] !== null) {
            $order->setDeliveredAt(new \DateTime($data['delivered_at']));
        }

        if ($data['refused_at'] !== null) {
            $order->setRefusedAt(new \DateTime($data['refused_at']));
        }

        if ($data['canceled_at'] !== null) {
            $order->setCanceledAt(new \DateTime($data['canceled_at']));
        }

        if (isset($data['shipments'][0]->amount)) {
            $order->setShipmentAmount(UpplerHelper::formatPrice($data['shipments'][0]['amount']));
        }

        return $order;
    }
}
