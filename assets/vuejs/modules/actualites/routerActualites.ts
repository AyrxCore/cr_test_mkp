import { RouteRecordRaw } from 'vue-router'
import ActualitesPage from '@/vuejs/modules/actualites/views/ActualitesList.vue'
import ActualiteStoryblokPage from '@/vuejs/modules/actualites/views/ActualiteStoryblok.vue'

import { NewsPageList } from '@/vuejs/router/pages-list'

export const routes: RouteRecordRaw[] = [
  {
    path: '/news',
    name: NewsPageList.NEWS,
    component: ActualitesPage,
  },
  {
    path: '/news/:slug',
    component: ActualiteStoryblokPage, // TODO: Adaptation DJUST - actualités à remplacer par ActualitePage si merge dans dev
    name: NewsPageList.NEWS_ITEM,
  },
]
