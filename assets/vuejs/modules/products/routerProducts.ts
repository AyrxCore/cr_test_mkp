import { RouteRecordRaw } from 'vue-router'
import ProductsPage from '@/vuejs/modules/products/views/ProductsList.vue'
import CategoriesPage from '@/vuejs/modules/products/views/CategoriesList.vue'
import ProductPage from '@/vuejs/modules/products/views/Product.vue'

export enum ProductPageList {
  PRODUCTS = 'products',
  CATEGORIES = 'categories',
  PRODUCT = 'product',
}

export const routes: RouteRecordRaw[] = [
  {
    path: '/app/products',
    name: ProductPageList.PRODUCTS,
    component: ProductsPage,
  },
  {
    path: '/app/categories',
    name: ProductPageList.CATEGORIES,
    component: CategoriesPage,
  },
  {
    path: '/app/product/:id',
    component: ProductPage,
    name: ProductPageList.PRODUCT,
  },
]
