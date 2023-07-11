import { RouteRecordRaw } from 'vue-router'
import CartPage from '@/vuejs/modules/cart/pages/CartPage.vue'
import RecapPage from '@/vuejs/modules/cart/views/Recap.vue'
import AddressPage from '@/vuejs/modules/cart/views/Addresses.vue'
import ShipmentsPage from '@/vuejs/modules/cart/views/Shipments.vue'
import PaymentPage from '@/vuejs/modules/cart/views/Payment.vue'
import ConfirmationPage from '@/vuejs/modules/cart/views/Confirmation.vue'
import PaymentErrorPage from '@/vuejs/modules/cart/views/PaymentError.vue'

import { CartPageList } from '@/vuejs/router/pages-list'

export const routes: RouteRecordRaw[] = [
  {
    path: '/cart',
    component: CartPage,
    children: [
      {
        path: '',
        component: RecapPage,
        name: CartPageList.CART_RECAP,
      },
      {
        path: 'addresses',
        component: AddressPage,
        name: CartPageList.CART_ADDRESSES,
      },
      {
        path: 'shipments',
        component: ShipmentsPage,
        name: CartPageList.CART_SHIPMENTS,
      },
      {
        path: 'payment',
        component: PaymentPage,
        name: CartPageList.CART_PAYMENT,
      },
      {
        path: 'confirmed/:id',
        component: ConfirmationPage,
        name: CartPageList.CART_CONFIRMED,
      },
      {
        path: 'payment-error',
        component: PaymentErrorPage,
        name: CartPageList.CART_PAYMENT_ERROR,
      },
    ],
  },
]
