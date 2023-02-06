import { RouteRecordRaw } from 'vue-router'
import PartnerPage from '@/vuejs/modules/partners/views/Partner.vue'
import ProductsPartnerPage from '@/vuejs/modules/partners/views/ProductsPartner.vue'

export enum PartnersPageList {
  PARTNER = 'partner',
  PRODUCTS_PARTNER = 'products-partner',
}

export const routes: RouteRecordRaw[] = [
  {
    path: `/app/${ PartnersPageList.PARTNER }/:id`,
    name: PartnersPageList.PARTNER,
    component: PartnerPage,
  },
  {
    path: '/app/' +PartnersPageList.PRODUCTS_PARTNER,
    component: ProductsPartnerPage,
    name: PartnersPageList.PRODUCTS_PARTNER,
  },
]
