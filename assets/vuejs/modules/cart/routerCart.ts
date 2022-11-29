import { RouteRecordRaw } from 'vue-router'
import RecapPage from '@/vuejs/modules/cart/views/Recap.vue'
import AddressesPage from '@/vuejs/modules/cart/views/Addresses.vue'
import ConfirmationPage from '@/vuejs/modules/cart/views/Confirmation.vue'

export enum CartPageList {
  CART = 'cart',
  RECAP = 'recap',
  ADDRESSES = 'addresses',
  CONFIRMATION = 'confirmation',
}

export const routes: RouteRecordRaw[] = [
  {
    path: '/app/cart',
    name: CartPageList.CART,
    redirect: '/app/cart/' + CartPageList.RECAP,
    children: [
      {
        path: CartPageList.RECAP,
        component: RecapPage,
        name: CartPageList.RECAP,
      },
      {
        path: CartPageList.ADDRESSES,
        component: AddressesPage,
        name: CartPageList.ADDRESSES,
      },
      {
        path: CartPageList.CONFIRMATION,
        component: ConfirmationPage,
        name: CartPageList.CONFIRMATION,
      },
    ]
  }
]
