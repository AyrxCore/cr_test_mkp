<?php

namespace App\Factory;

use App\Dto\Order;
use App\Helper\UpplerHelper;
use Exception;

class OrderFactory
{
    /**
     * @throws Exception
     */
    public static function createFromUpplerResponse(mixed $remoteOrder): Order
    {
        $order = new Order();
        $order->setId($remoteOrder->id);
        $order->setOrderNumber(UpplerHelper::getOrderNumber($remoteOrder));
        $order->setTotal(UpplerHelper::formatPrice($remoteOrder->total));
        $order->setTotalExcludingTaxes(UpplerHelper::formatPrice($remoteOrder->total_excluding_taxes));
        $order->setState($remoteOrder->state);

        $shippingAddress = $remoteOrder->shipping_address;
        $country = $shippingAddress->country->name->default;
        $shippingAddress = sprintf("%s %s %s, %s", $shippingAddress->street, $shippingAddress->postcode, $shippingAddress->city, $country);
        $order->setShippingAddress($shippingAddress);
        $order->setShippingState($remoteOrder->shipping_state);
        $order->setCreatedAt(new \DateTime($remoteOrder->created_at));
        $order->setUpdatedAt(new \DateTime($remoteOrder->updated_at));
        $order->setItems($remoteOrder->items);

        $billingAddress = $remoteOrder->billing_address;
        $country = $billingAddress->country->name->default;
        $billingAddress = sprintf("%s %s %s, %s", $billingAddress->street, $billingAddress->postcode, $billingAddress->city, $country);
        $order->setBillingAddress($billingAddress);
        if ($remoteOrder->payment_state === 'payout') {
            $order->setPaymentId($remoteOrder->payment->id);
        }

        if (null !== $remoteOrder->shipped_at) {
            $order->setShippedAt(new \DateTime($remoteOrder->shipped_at));
        }

        if (null !== $remoteOrder->confirmed_at) {
            $order->setConfirmedAt(new \DateTime($remoteOrder->confirmed_at));
        }

        if (null !== $remoteOrder->delivered_at) {
            $order->setDeliveredAt(new \DateTime($remoteOrder->delivered_at));
        }

        if (null !== $remoteOrder->refused_at) {
            $order->setRefusedAt(new \DateTime($remoteOrder->refused_at));
        }

        if (null !== $remoteOrder->canceled_at) {
            $order->setCanceledAt(new \DateTime($remoteOrder->canceled_at));
        }

        if (isset($remoteOrder->shipments[0]->amount)) {
            $order->setShipmentAmount(UpplerHelper::formatPrice($remoteOrder->shipments[0]->amount));
        }

        return $order;
    }
}
