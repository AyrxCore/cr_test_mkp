import { RouteRecordRaw } from 'vue-router'
import ProductsPage from '@/vuejs/modules/products/components/ProductsList.vue'
import ProductPage from '@/vuejs/modules/products/views/Product.vue'

export enum ProductPageList {
  PRODUCTS = 'products',
  PRODUCT = 'product',
}

export const routes: RouteRecordRaw[] = [
  {
    path: '/app/' + ProductPageList.PRODUCTS,
    component: ProductsPage,
    name: ProductPageList.PRODUCTS,
    children: [
      {
        path: '/app/' + ProductPageList.PRODUCT,
        component: ProductPage,
        name: ProductPageList.PRODUCT,
      },
    ],
  },
]
