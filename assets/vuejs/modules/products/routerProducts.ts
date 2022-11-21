import { RouteRecordRaw } from 'vue-router'
import ProductsPage from '@/vuejs/modules/products/views/ProductsList.vue'
import ProductPage from '@/vuejs/modules/products/views/Product.vue'

export enum ProductPageList {
  PRODUCTS = 'products',
  PRODUCT = 'product',
}

export const routes: RouteRecordRaw[] = [
  {
    path: '/app/products',
    name: ProductPageList.PRODUCTS,
    component: ProductsPage,
  },
  {
    path: '/app/product',
    component: ProductPage,
    name: ProductPageList.PRODUCT,
  },
]
