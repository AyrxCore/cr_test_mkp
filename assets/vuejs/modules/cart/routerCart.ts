import { RouteRecordRaw } from 'vue-router'
import RecapPage from '@/vuejs/modules/cart/views/Recap.vue'
import AddressPage from '@/vuejs/modules/cart/views/Addresses.vue'
import CartPage from '@/vuejs/modules/cart/pages/CartPage.vue'
import ConfirmationPage from '@/vuejs/modules/cart/views/Confirmation.vue'
import PaymentErrorPage from '@/vuejs/modules/cart/views/PaymentError.vue'
import PaymentPage from '@/vuejs/modules/cart/views/Payment.vue'

import { CartPageList } from '@/vuejs/router/pages-list'

export const routes: RouteRecordRaw[] = [
  {
    path: '/cart',
    component: CartPage,
    children: [
      {
        path: '',
        component: RecapPage,
        name: CartPageList.RECAP,
      },
      {
        path: 'addresses',
        component: AddressPage,
        name: CartPageList.ADDRESSES,
      },
      {
        path: 'payment',
        component: PaymentPage,
        name: CartPageList.PAYMENT,
      },
      {
        path: 'confirmed',
        component: ConfirmationPage,
        name: CartPageList.CONFIRMED,
      },
      {
        path: 'payment-error',
        component: PaymentErrorPage,
        name: CartPageList.PAYMENT_ERROR,
      },
    ],
  },
]
