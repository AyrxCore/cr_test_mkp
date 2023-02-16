<template>
  <nav class="bg-grey-light w-full rounded-md text-xs">
    <ol class="list-reset text-xs hidden lg:flex">
      <li>
        <RouterLink :to="{ path: '/app/home' }" class="text-gray-400">
          Accueil
        </RouterLink>
      </li>
      <ChevronRightIconComponent
        class="ml-1 h-4 text-gray-500"
        :stroke-color="'#A4A4A4'"
      />
      <li v-for="(list, key) in listUrl" :key="key" class="inline-flex">
        <RouterLink :to="{ path: list.url ?? '#' }" class="text-gray-400">{{
          list.name
        }}</RouterLink>
        <ChevronRightIconComponent
          class="ml-1 h-4 text-gray-500"
          :stroke-color="'#A4A4A4'"
        />
      </li>
      <li class="text-gray-400">{{ currentPage }}</li>
    </ol>
    <div
      v-if="lastBreadcrumbUrl"
      class="flex lg:hidden"
    >
      <ChevronRightIconComponent
        class="mr-1 h-4 text-gray-500 rotate-180"
        :stroke-color="'#A4A4A4'"
      />
      <RouterLink :to="{ path: lastBreadcrumbUrl.url ?? '#' }" class="text-gray-400">{{
          lastBreadcrumbUrl.name
        }}</RouterLink>
    </div>

  </nav>
</template>

<script lang="ts" setup>
import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/ChevronRightIconComponent.vue'
import { computed, PropType } from 'vue'

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
const lastBreadcrumbUrl = computed(() => {  if (props.listUrl) {    return props.listUrl[props.listUrl.length - 1]  }  return []})

const lastBreadcrumbUrl = computed(() => {
  if (props.listUrl) {
    return props.listUrl[props.listUrl.length - 1]
  }

  return null
})
</script>

<style scoped></style>
