<template>
  <nav class="bg-grey-light w-full rounded-md text-xs">
    <ol class="list-reset flex text-xs">
      <li><a href="/app/home" class="text-gray-400">Accueil</a></li>
      <ChevronRightIconComponent
        class="ml-1 h-4 text-gray-500"
        :stroke-color="'#A4A4A4'"
      />
      <li v-for="(list, key) in listUrl" :key="key" class="inline-flex">
        <a :href="list.url ?? '#'" class="text-gray-400">{{ list.name }}</a>
        <ChevronRightIconComponent
          class="ml-1 h-4 text-gray-500"
          :stroke-color="'#A4A4A4'"
        />
      </li>
      <li class="text-gray-400">{{ currentPage }}</li>
    </ol>
  </nav>
</template>

<script lang="ts" setup>
import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/ChevronRightIconComponent.vue'
import { PropType } from 'vue'

const props = defineProps({
  listUrl: {
    required: false,
    type: Object as PropType<any[]>,
    default: [],
  },
  currentPage: {
    required: true,
    type: String,
  },
})
</script>

<style scoped></style>
