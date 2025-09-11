<template>
  <nav class="w-full text-sm text-black">
    <ol class="list-reset hidden text-xs lg:flex">
      <li class="inline-flex items-center">
        <RouterLink
          :to="{ name: PageList.HOME_PAGE }"
          @click="
            sendGtmEvent('breadcrumb_click', {
              link_text: $event.target.innerText,
              link_url: router.resolve({
                name: PageList.HOME_PAGE,
              }).fullPath,
              origin_url: router.currentRoute.value.fullPath,
            })
          "
          >Accueil
        </RouterLink>
        <ChevronRightIconComponent class="mx-1 h-3" />
      </li>
      <li
        v-for="(list, key) in listUrl"
        :key="key"
        class="inline-flex items-center"
      >
        <RouterLink
          :to="list.url ?? '#'"
          @click="
            sendGtmEvent('breadcrumb_click', {
              link_text: $event.target.innerText,
              link_url: list.url ? router.resolve(list.url).fullPath : '#',
              origin_url: router.currentRoute.value.fullPath,
            })
          "
          >{{ list.name }}
        </RouterLink>
        <ChevronRightIconComponent class="mx-1 h-3" />
      </li>
      <li class="font-extrabold">{{ currentPage }}</li>
    </ol>
    <RouterLink
      v-if="lastBreadcrumbUrl"
      :to="lastBreadcrumbUrl.url ?? '#'"
      class="inline-flex items-center lg:hidden"
    >
      <ChevronRightIconComponent class="mr-1 h-3 rotate-180" />
      {{ lastBreadcrumbUrl.name }}
    </RouterLink>
  </nav>
</template>

<script lang="ts" setup>
import { computed, PropType } from 'vue'

import router, { PageList } from '@/vuejs/router'
import { sendGtmEvent } from '@/vuejs/services/gtm'

import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/Chevron2RightIconComponent.vue'

const props = defineProps({
  listUrl: {
    required: false,
    type: Object as PropType<any[]>,
    default: null,
  },
  currentPage: {
    required: true,
    type: String,
  },
})

const lastBreadcrumbUrl = computed(() => {
  if (props.listUrl) {
    return props.listUrl[props.listUrl.length - 1]
  }

  return null
})
</script>
