<?php

declare(strict_types=1);

namespace App\Enum\Djust;

enum DjustApiEndpoint: string
{
    // Auth
    case AUTH_TOKEN = '/auth/token';
    case REFRESH_TOKEN = '/auth/refresh-token';

    // Front
    case SHOP_COMMERCIAL_ORDERS = '/v2/shop/commercial-orders';
    case SHOP_COMMERCIAL_ORDER_BILLING_ADDRESS = '/v2/shop/commercial-orders/%s/billing-information';
    case SHOP_COMMERCIAL_ORDER_BY_ID = '/v1/shop/commercial-orders/%s';
    case SHOP_COMMERCIAL_ORDER_DELETE = '/v2/shop/commercial-orders/%s';
    case SHOP_COMMERCIAL_ORDERS_ITEMS = '/v2/shop/commercial-orders/%s/lines';
    case SHOP_COMMERCIAL_ORDER_SHIPPING_ADDRESS = '/v2/shop/commercial-orders/%s/shipping-information';
    case SHOP_COMMERCIAL_ORDER_SYNC = '/v1/shop/commercial-orders/%s/sync';
    case SHOP_CUSTOMER_ACCOUNTS = '/v1/shop/customer-accounts';
    case SHOP_ADDRESSES = '/v1/shop/customer-accounts/addresses';
    case SHOP_ADDRESS_BY_ID = '/v1/shop/customer-accounts/addresses/%s';
    case SHOP_NAVIGATION_CATEGORY_ONLINE = '/v1/shop/navigation-category/online';
    case SHOP_OFFER_PRICES = '/v1/shop/offer-prices';
    case SHOP_PAYMENTS_METHODS = '/v1/shop/payments/payment-methods';
    case SHOP_PAYMENTS_INITIATE = '/v1/shop/payments';
    case SHOP_PAYMENTS_DETAILS = '/v1/shop/payments/details';
    case SHOP_PRODUCT_BY_ID = '/v1/shop/products/%s';
    case SHOP_PRODUCT_OFFERS = '/v1/shop/products/%s/paginated-offers';
    case SHOP_SEARCH = '/v2/shop/search';
    case SHOP_SUPPLIERS = '/v1/shop/suppliers';
    case SHOP_LOGISTIC_ORDER = '/v2/shop/logistic-orders/%s';
    case SHOP_LOGISTIC_ORDER_LINES = '/v1/shop/logistic-orders/%s/lines';

    // Back
    case OPERATOR_CUSTOMER_USERS = '/v1/customer-users';
}
