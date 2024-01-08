<template>
  <nav class="w-full text-sm text-black">
    <ol class="list-reset hidden text-xs lg:flex">
      <li class="inline-flex items-center">
        <RouterLink :to="{ name: PageList.HOME_PAGE }">Accueil</RouterLink>
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
            sendGaEvent(gtmEventName, {
              product_name: list.name,
            })
          "
          >{{ list.name }}</RouterLink
        >
        <ChevronRightIconComponent class="mx-1 h-3" />
      </li>
      <li class="font-extrabold">{{ currentPage }}</li>
    </ol>
    <RouterLink
      v-if="lastBreadcrumbUrl"
      class="inline-flex items-center lg:hidden"
      :to="lastBreadcrumbUrl.url ?? '#'"
      @click="
        sendGaEvent(gtmEventName, {
          product_name: lastBreadcrumbUrl.name,
        })
      "
    >
      <ChevronRightIconComponent class="mr-1 h-3 rotate-180" />
      {{ lastBreadcrumbUrl.name }}
    </RouterLink>
  </nav>
</template>

<script lang="ts" setup>
import { computed, PropType } from 'vue'

import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/Chevron2RightIconComponent.vue'

import { PageList } from '@/vuejs/router'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

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
  gtmEventName: {
    type: String,
    default: '',
  },
})

const lastBreadcrumbUrl = computed(() => {
  if (props.listUrl) {
    return props.listUrl[props.listUrl.length - 1]
  }

  return null
})
</script>
