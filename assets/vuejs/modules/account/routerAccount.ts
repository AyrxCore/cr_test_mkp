import { RouteRecordRaw } from 'vue-router'
import MyAccountPage from '@/vuejs/modules/account/views/MyAccount.vue'
import AddressesPage from '@/vuejs/modules/account/views/Addresses.vue'
import ContactInformationPage from '@/vuejs/modules/account/views/ContactInformation.vue'
import FavoritesProductsPage from '@/vuejs/modules/account/views/FavoritesProducts.vue'
import FavoritesProductsDetailsPage from '@/vuejs/modules/account/views/FavoritesProductsDetails.vue'
import OrdersHistoryPage from '@/vuejs/modules/account/views/OrdersHistory.vue'
import SavedCartsPage from '@/vuejs/modules/account/views/SavedCarts.vue'
import OrdersValidationPage from '@/vuejs/modules/account/views/OrdersValidation.vue'

export enum AccountPageList {
  ACCOUNT = 'account',
  CONTACT_INFORMATION = 'contact-information',
  ADDRESSES = 'addresses',
  FAVORIS_LIST = 'favoris-list',
  FAVORIS_DETAILS = 'favoris-details',
  ORDERS_HISTORY = 'orders-history',
  SAVED_CARTS = 'saved-carts',
  ORDERS_VALIDATION = 'orders-validation',
}

export const accountUrl = '/app/' + AccountPageList.ACCOUNT
export const baseUrl = accountUrl + '/'

export const routes: RouteRecordRaw[] = [
  {
    path: accountUrl,
    children: [
      {
        path: '',
        component: MyAccountPage,
        name: AccountPageList.CONTACT_INFORMATION,
      },
      {
        path: baseUrl + AccountPageList.CONTACT_INFORMATION,
        component: ContactInformationPage,
        name: AccountPageList.ACCOUNT,
      },
      {
        path: baseUrl + AccountPageList.ADDRESSES,
        component: AddressesPage,
        name: AccountPageList.ADDRESSES,
      },
      {
        path: baseUrl + AccountPageList.FAVORIS_LIST,
        component: FavoritesProductsPage,
        name: AccountPageList.FAVORIS_LIST,
      },
      {
        path: baseUrl + AccountPageList.FAVORIS_DETAILS,
        component: FavoritesProductsDetailsPage,
        name: AccountPageList.FAVORIS_DETAILS,
      },
      {
        path: baseUrl + AccountPageList.ORDERS_HISTORY,
        component: OrdersHistoryPage,
        name: AccountPageList.ORDERS_HISTORY,
      },
      {
        path: baseUrl + AccountPageList.SAVED_CARTS,
        component: SavedCartsPage,
        name: AccountPageList.SAVED_CARTS,
      },
      {
        path: baseUrl + AccountPageList.ORDERS_VALIDATION,
        component: OrdersValidationPage,
        name: AccountPageList.ORDERS_VALIDATION,
      },
    ],
  },
]
