import { RouteRecordRaw } from 'vue-router'
import MyAccountPage from '@/vuejs/modules/account/views/MyAccount.vue'
import AddressesPage from '@/vuejs/modules/account/views/adresses/AddressesContainer.vue'
import AddressCreate from '@/vuejs/modules/account/views/adress/AddressCreate.vue'
import AddressEdit from '@/vuejs/modules/account/views/adress/AddressEdit.vue'
import ProfileInformationsReadonly from '@/vuejs/modules/account/views/ProfileInformations/ProfileInformationsReadonly.vue'
import ProfilePasswordChangeForm from '@/vuejs/modules/account/views/ProfileInformations/ProfilePasswordChangeForm.vue'
import ProfileEmailEditForm from '@/vuejs/modules/account/views/ProfileInformations/ProfileEmailEditForm.vue'
import ProfileDetailsEditForm from '@/vuejs/modules/account/views/ProfileInformations/ProfileDetailsEditForm.vue'
import FavoritesProductsPage from '@/vuejs/modules/account/views/FavoritesProducts.vue'
import FavoritesProductsDetailsPage from '@/vuejs/modules/account/views/FavoritesProductsDetails.vue'
import OrdersHistoryPage from '@/vuejs/modules/account/views/OrdersHistory.vue'
import SavedCartsPage from '@/vuejs/modules/account/views/SavedCarts.vue'
import OrdersValidationPage from '@/vuejs/modules/account/views/OrdersValidation.vue'

export enum AccountPageList {
  ACCOUNT = 'account',
  CONTACT_INFORMATION = 'contact-information',
  CONTACT_INFORMATION_PASSWORD_CHANGE = 'contact-information-password-change',
  CONTACT_INFORMATION_EMAIL_EDIT = 'contact-information-email-edit',
  CONTACT_INFORMATION_DETAILS_EDIT = 'contact-information-details-edit',
  ADDRESSES = 'addresses',
  ADDRESS_EDIT = 'address-edit',
  ADDRESS_CREATE = 'address-create',
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
        component: ProfileInformationsReadonly,
        name: AccountPageList.ACCOUNT,
      },
      {
        path: baseUrl + AccountPageList.CONTACT_INFORMATION_PASSWORD_CHANGE,
        component: ProfilePasswordChangeForm,
        name: AccountPageList.CONTACT_INFORMATION_PASSWORD_CHANGE,
      },
      {
        path: baseUrl + AccountPageList.CONTACT_INFORMATION_EMAIL_EDIT,
        component: ProfileEmailEditForm,
        name: AccountPageList.CONTACT_INFORMATION_EMAIL_EDIT,
      },
      {
        path: baseUrl + AccountPageList.CONTACT_INFORMATION_DETAILS_EDIT,
        component: ProfileDetailsEditForm,
        name: AccountPageList.CONTACT_INFORMATION_DETAILS_EDIT,
      },
      {
        path: baseUrl + AccountPageList.ADDRESSES,
        component: AddressesPage,
        name: AccountPageList.ADDRESSES,
      },
      {
        path: baseUrl + AccountPageList.ADDRESS_CREATE + '/:type',
        component: AddressCreate,
        name: AccountPageList.ADDRESS_CREATE,
        props: true,
      },
      {
        path: baseUrl + AccountPageList.ADDRESS_EDIT + '/:id',
        component: AddressEdit,
        name: AccountPageList.ADDRESS_EDIT,
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
