import { RouteRecordRaw } from 'vue-router'
import MyAccountPage from '@/vuejs/modules/account/views/MyAccount.vue'
import AddressesPage from '@/vuejs/modules/account/views/addresses/AddressesContainer.vue'
import AddressCreate from '@/vuejs/modules/account/views/address/AddressCreate.vue'
import AddressEdit from '@/vuejs/modules/account/views/address/AddressEdit.vue'
import ProfileInformationsReadonly from '@/vuejs/modules/account/views/ProfileInformations/ProfileInformationsReadonly.vue'
import ProfilePasswordChangeForm from '@/vuejs/modules/account/views/ProfileInformations/ProfilePasswordChangeForm.vue'
import ProfileEmailEditForm from '@/vuejs/modules/account/views/ProfileInformations/ProfileEmailEditForm.vue'
import ProfileDetailsEditForm from '@/vuejs/modules/account/views/ProfileInformations/ProfileDetailsEditForm.vue'
import FavoritesProductsPage from '@/vuejs/modules/account/views/FavoritesProducts.vue'
import FavoritesProductsDetailsPage from '@/vuejs/modules/account/views/FavoritesProductsDetails.vue'
import SavedCartDetailsPage from '@/vuejs/modules/account/views/SavedCartsDetails.vue'
import OrdersPage from '@/vuejs/modules/account/views/Orders.vue'
import OrderDetailsPage from '@/vuejs/modules/account/views/OrderDetails.vue'
import SavedCartsPage from '@/vuejs/modules/account/views/SavedCarts.vue'
import OrdersValidationPage from '@/vuejs/modules/account/views/OrdersValidation.vue'

import { AccountPageList } from '@/vuejs/router/pages-list'
import Dashboard from '@/vuejs/modules/account/views/Dashboard.vue'

export const routes: RouteRecordRaw[] = [
  {
    path: '/account',
    children: [
      {
        path: '',
        component: MyAccountPage,
        name: AccountPageList.CONTACT_INFORMATION,
      },
      {
        path: 'details',
        component: ProfileInformationsReadonly,
        name: AccountPageList.ACCOUNT,
      },
      {
        path: 'password',
        component: ProfilePasswordChangeForm,
        name: AccountPageList.CONTACT_INFORMATION_PASSWORD_CHANGE,
      },
      {
        path: 'email',
        component: ProfileEmailEditForm,
        name: AccountPageList.CONTACT_INFORMATION_EMAIL_EDIT,
      },
      {
        path: 'details/edit',
        component: ProfileDetailsEditForm,
        name: AccountPageList.CONTACT_INFORMATION_DETAILS_EDIT,
      },
      {
        path: 'addresses',
        component: AddressesPage,
        name: AccountPageList.ADDRESSES,
      },
      {
        path: 'addresses/create/:type',
        component: AddressCreate,
        name: AccountPageList.ADDRESS_CREATE,
        props: true,
      },
      {
        path: 'addresses/:id',
        component: AddressEdit,
        name: AccountPageList.ADDRESS_EDIT,
      },
      // TODO (MKP-1411): Guard temporaire - retirer beforeEnter et rétablir les liens UI
      // quand les Favoris seront disponibles via DJUST.
      {
        path: 'favorites',
        component: FavoritesProductsPage,
        name: AccountPageList.FAVORITES_LIST,
        beforeEnter: () => ({ name: AccountPageList.ACCOUNT }), // TODO (MKP-1411): supprimer cette ligne
      },
      {
        path: 'favorites/:id',
        component: FavoritesProductsDetailsPage,
        name: AccountPageList.FAVORITES_DETAILS,
        beforeEnter: () => ({ name: AccountPageList.ACCOUNT }), // TODO (MKP-1411): supprimer cette ligne
      },
      {
        path: 'orders',
        component: OrdersPage,
        name: AccountPageList.ORDERS,
      },
      {
        path: 'orders/:id',
        component: OrderDetailsPage,
        name: AccountPageList.ORDER_DETAILS,
      },
      // TODO (MKP-1411): Guard temporaire - retirer beforeEnter et rétablir les liens UI
      // quand les Paniers sauvegardés seront disponibles via DJUST.
      {
        path: 'saved-carts',
        component: SavedCartsPage,
        name: AccountPageList.SAVED_CARTS,
        beforeEnter: () => ({ name: AccountPageList.ACCOUNT }), // TODO (MKP-1411): supprimer cette ligne
      },
      {
        path: 'saved-carts/:id',
        component: SavedCartDetailsPage,
        name: AccountPageList.SAVED_CARTS_DETAILS,
        beforeEnter: () => ({ name: AccountPageList.ACCOUNT }), // TODO (MKP-1411): supprimer cette ligne
      },
      {
        path: 'orders/validation',
        component: OrdersValidationPage,
        name: AccountPageList.ORDERS_VALIDATION,
      },
      {
        path: 'dashboard',
        component: Dashboard,
        name: AccountPageList.DASHBOARD,
      },
    ],
  },
]
