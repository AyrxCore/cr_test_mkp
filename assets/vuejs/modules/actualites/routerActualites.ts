import { RouteRecordRaw } from 'vue-router'
import ActualitesPage from '@/vuejs/modules/actualites/views/ActualitesList.vue'
import ActualitePage from '@/vuejs/modules/actualites/views/Actualite.vue'

import { NewsPageList } from '@/vuejs/router/pages-list'

export const routes: RouteRecordRaw[] = [
  {
    path: '/news',
    name: NewsPageList.NEWS,
    component: ActualitesPage,
  },
  {
    path: '/news/:slug',
    component: ActualitePage,
    name: NewsPageList.NEWS_ITEM,
  },
]
