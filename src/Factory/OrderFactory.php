<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\Order;
use App\Enum\Djust\DjustCustomField;

class OrderFactory extends AbstractFactory
{
    private const string FDP_EXTERNAL_ID_PREFIX = 'PRODUCT_FDP_';
    private const string INVOICE_CUSTOM_FIELD_ID = 'COMMERCIAL_ORDER_PARTNER_INVOICE';
    private const string DEFAULT_LOGISTIC_STATUS = 'DRAFT_ORDER';

    public function create(mixed $data): Order
    {
        $order = new Order();
        $orderLogistics = $data['orderLogistics'] ?? [];
        $firstLogistic = !empty($orderLogistics) ? $orderLogistics[0] : [];

        $this->setOrderBasicInfo($order, $data);
        $this->setOrderPrices($order, $data['orderLogisticPrices'] ?? []);
        $this->setOrderShippingStatus($order, $firstLogistic['status'] ?? self::DEFAULT_LOGISTIC_STATUS);
        $this->setOrderAddresses($order, $firstLogistic);
        $this->setOrderDates($order, $data);
        $this->setOrderItems($order, $orderLogistics);
        $baseRef = $data['reference'] ?? '';
        $this->setOrderInvoice($order, $orderLogistics, $baseRef);
        $this->setOrderPartners($order, $orderLogistics, $baseRef);

        return $order;
    }

    private function setOrderBasicInfo(Order $order, array $data): void
    {
        $order->setId((int) \ltrim($data['id'] ?? '0', '0'));
        $order->setOrderNumber($data['reference'] ?? '');
    }

    private function setOrderPrices(Order $order, array $prices): void
    {
        $order->setTotal((float) ($prices['totalPriceWithTax'] ?? 0));
    }

    private function isFdpLine(array $line): bool
    {
        $externalId = $line['orderLogisticLineProductDto']['externalId'] ?? '';

        return \str_starts_with($externalId, self::FDP_EXTERNAL_ID_PREFIX);
    }

    private function setOrderShippingStatus(Order $order, string $status): void
    {
        $order->setShippingState($this->mapDjustShippingStatus($status));
    }

    private function setOrderAddresses(Order $order, array $logistic): void
    {
        if (isset($logistic['shippingTrackingUrl'])) {
            $order->setShippingTrackingUrl($logistic['shippingTrackingUrl']);
        }

        if (isset($logistic['shippingAddressSnapshot']) && $logistic['shippingAddressSnapshot'] !== null) {
            $formattedAddress = $this->formatAddress($logistic['shippingAddressSnapshot']);
            if ($formattedAddress) {
                $order->setShippingAddress($formattedAddress);
            }
        }

        if (isset($logistic['billingAddressSnapshot']) && $logistic['billingAddressSnapshot'] !== null) {
            $formattedAddress = $this->formatAddress($logistic['billingAddressSnapshot']);
            if ($formattedAddress) {
                $order->setBillingAddress($formattedAddress);
            }
        }
    }

    private function setOrderDates(Order $order, array $data): void
    {
        $order->setCreatedAt(new \DateTime($data['createdAt'] ?? 'now'));
        $order->setUpdatedAt(new \DateTime($data['updatedAt'] ?? 'now'));
        $this->setDateIfNotNull($order, 'setConfirmedAt', $data['validatedAt'] ?? null);
    }

    private function setDateIfNotNull(Order $order, string $setter, ?string $date): void
    {
        if ($date !== null) {
            $order->$setter(new \DateTime($date));
        }
    }

    private function setOrderItems(Order $order, array $orderLogistics): void
    {
        $allLines = [];
        $totalProductCount = 0;
        $totalExcludingTaxes = 0.0;
        $shipmentAmount = 0.0;

        foreach ($orderLogistics as $logistic) {
            $lines = $logistic['lines'] ?? [];
            $supplierSnapshot = $logistic['supplierSnapshot'] ?? [];
            foreach ($lines as $line) {
                $price = $line['orderLogisticLinePriceDto'] ?? [];
                if ($this->isFdpLine($line)) {
                    $shipmentAmount += (float) ($price['totalPriceWithoutTaxes'] ?? 0);
                    continue;
                }
                $allLines[] = ['line' => $line, 'supplier' => $supplierSnapshot];
                $totalProductCount += (int) ($line['quantity'] ?? 0);
                $totalExcludingTaxes += (float) ($price['totalPriceWithoutTaxes'] ?? 0);
            }
        }

        $order->setProductCount($totalProductCount);
        $order->setItems($this->mapItems($allLines));
        $order->setTotalExcludingTaxes($totalExcludingTaxes);
        $order->setShipmentAmount($shipmentAmount);
    }

    private function setOrderInvoice(Order $order, array $orderLogistics, string $baseReference): void
    {
        $links = [];
        foreach ($orderLogistics as $logistic) {
            $subRef = $logistic['reference'] ?? $baseReference;
            $invoiceUrl = $this->extractInvoiceUrlFromLogistic($logistic);
            $links[] = ['reference' => $subRef, 'invoiceUrl' => $invoiceUrl];
        }
        $hasSubRefs = $this->hasSubReferences($orderLogistics, $baseReference);
        if ($hasSubRefs && \count($links) >= 2) {
            $order->setOrderInvoiceLinks($links);
            $firstWithUrl = \array_values(\array_filter($links, static fn ($l) => !empty($l['invoiceUrl'])))[0] ?? null;
            $order->setInvoiceUrl($firstWithUrl !== null ? $firstWithUrl['invoiceUrl'] : null);
        } else {
            $withUrl = \array_values(\array_filter($links, static fn ($l) => !empty($l['invoiceUrl'])));
            if (\count($withUrl) === 1) {
                $order->setInvoiceUrl($withUrl[0]['invoiceUrl']);
            } elseif (!empty($withUrl)) {
                $order->setInvoiceUrl($withUrl[0]['invoiceUrl']);
            }
        }
    }

    private function extractInvoiceUrlFromLogistic(array $logistic): ?string
    {
        $customFieldValues = $logistic['customFieldValues'] ?? [];
        foreach ($customFieldValues as $customField) {
            $externalId = $customField['customField']['externalId'] ?? '';
            if ($externalId === self::INVOICE_CUSTOM_FIELD_ID && isset($customField['value'])) {
                return $customField['value'];
            }
        }

        return null;
    }

    private function hasSubReferences(array $orderLogistics, string $baseReference): bool
    {
        if (\count($orderLogistics) < 2) {
            return false;
        }
        foreach ($orderLogistics as $logistic) {
            $ref = $logistic['reference'] ?? '';
            if ($ref === $baseReference || !\preg_match('/-\d+$/', $ref)) {
                return false;
            }
        }

        return true;
    }

    private function setOrderPartners(Order $order, array $orderLogistics, string $baseReference): void
    {
        if (!$this->hasSubReferences($orderLogistics, $baseReference)) {
            return;
        }
        $partners = [];
        foreach ($orderLogistics as $logistic) {
            $lines = $logistic['lines'] ?? [];
            $supplierSnapshot = $logistic['supplierSnapshot'] ?? [];
            $linesWithSupplier = [];
            foreach ($lines as $line) {
                if ($this->isFdpLine($line)) {
                    continue;
                }
                $linesWithSupplier[] = ['line' => $line, 'supplier' => $supplierSnapshot];
            }
            $logisticStatus = $logistic['status'] ?? self::DEFAULT_LOGISTIC_STATUS;
            $partners[] = [
                'reference' => $logistic['reference'] ?? $baseReference,
                'partnerName' => $supplierSnapshot['name'] ?? '',
                'shippingState' => $this->mapDjustShippingStatus($logisticStatus),
                'shippingTrackingUrl' => $logistic['shippingTrackingUrl'] ?? null,
                'invoiceUrl' => $this->extractInvoiceUrlFromLogistic($logistic),
                'items' => $this->mapItems($linesWithSupplier),
            ];
        }
        \usort($partners, static function ($a, $b) {
            \preg_match('/-(\d+)$/', $a['reference'], $matchA);
            \preg_match('/-(\d+)$/', $b['reference'], $matchB);
            $numA = isset($matchA[1]) ? (int) $matchA[1] : 0;
            $numB = isset($matchB[1]) ? (int) $matchB[1] : 0;

            return $numA <=> $numB;
        });
        $order->setOrderPartners($partners);
    }

    private function formatAddress(array $address): string
    {
        $addressParts = \array_filter([
            $address['fullName'] ?? '',
            $address['address'] ?? '',
            $address['additionalAddress'] ?? '',
            ($address['zipcode'] ?? '').' '.($address['city'] ?? ''),
            $address['country'] ?? '',
        ]);

        return \implode(', ', $addressParts);
    }

    private function mapItems(array $linesWithSupplier): array
    {
        return \array_map(function ($item) {
            $line = $item['line'] ?? [];
            $supplierSnapshot = $item['supplier'] ?? [];

            $product = $line['orderLogisticLineProductDto'] ?? [];
            $variant = $line['orderLogisticLineProductVariantDto'] ?? [];
            $price = $line['orderLogisticLinePriceDto'] ?? [];

            $mainImageUrl = $variant['mainImageUrl'] ?? $product['mainImageUrl'] ?? '';
            $unitPrice = $price['itemPriceWithoutTaxes'] ?? 0;

            $ecoTax = $this->extractEcoTaxFromProductAttributes($product['productAttributeValues'] ?? []);

            return [
                'quantity' => $line['quantity'] ?? 0,
                'unit_price' => (float) $unitPrice,
                'total_excluding_taxes' => (int) \round(($price['totalPriceWithoutTaxes'] ?? 0) * 100),
                'eco_tax' => $ecoTax,
                'variant' => [
                    'sku' => $variant['sku'] ?? '',
                    'mainImageUrl' => $mainImageUrl,
                    'product' => [
                        'id' => $product['djustProductUuid'] ?? $product['externalId'] ?? '',
                        'externalId' => $product['externalId'] ?? $product['djustProductUuid'] ?? '',
                        'name' => [
                            'default' => $product['name'] ?? '',
                        ],
                        'reference' => $variant['externalReference'] ?? $variant['sku'] ?? '',
                        'images' => $mainImageUrl ? [$mainImageUrl] : [],
                        'seller' => [
                            'name' => $supplierSnapshot['name'] ?? '',
                        ],
                    ],
                ],
            ];
        }, $linesWithSupplier);
    }

    private function extractEcoTaxFromProductAttributes(array $productAttributeValues): ?float
    {
        foreach ($productAttributeValues as $attr) {
            if (($attr['attributeExternalId'] ?? '') === DjustCustomField::PRODUCT_ECOTAXE->value) {
                $value = $attr['attributeValue'] ?? null;
                if ($value !== null && $value !== '') {
                    return (float) $value;
                }
                break;
            }
        }

        return null;
    }

    private function mapDjustShippingStatus(string $djustStatus): string
    {
        return match ($djustStatus) {
            'CREATING', 'DRAFT_ORDER', 'DRAFT_ORDER_ON_HOLD', 'ORDER_CREATED', 'CREATED' => Order::SHIPPING_PENDING,
            'PENDING_SUPPLIER_CONFIRMATION', 'WAITING_SUPPLIER_APPROVAL', 'WAITING_CUSTOMER_APPROVAL' => Order::SHIPPING_PENDING,
            'BLOCKED_BY_POLICY', 'BLOCKED_BY_PAYMENT' => Order::SHIPPING_PENDING,
            'ACCEPTED_BY_SUPPLIER', 'CONFIRMED', 'WAITING_SHIPMENT' => Order::SHIPPING_PREPARATION,
            'READY_TO_SHIP' => Order::SHIPPING_READY,
            'PARTIALLY_SHIPPED' => Order::SHIPPING_PARTIALLY_SHIPPED,
            'SHIPPED' => Order::SHIPPING_SHIPPED,
            'COMPLETED', 'DELIVERED' => Order::SHIPPING_DELIVERED,
            'RETURNED' => Order::SHIPPING_RETURNED,
            'DECLINED_BY_CUSTOMER', 'DECLINED_BY_SUPPLIER', 'DECLINED', 'EXPIRED', 'CANCELED', 'CANCELLED', 'PARTIALLY_CANCELED', 'REFUSED' => Order::SHIPPING_CANCELLED,
            default => Order::SHIPPING_PENDING,
        };
    }
}
