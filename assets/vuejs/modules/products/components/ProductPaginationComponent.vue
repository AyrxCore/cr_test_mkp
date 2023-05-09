<template>
  <div
    class="mt-5 flex w-full items-center justify-center rounded-md p-2 md:justify-end"
  >
    <div
      class="mr-2 flex h-[28px] w-auto flex-row items-center justify-between rounded-md border bg-white"
    >
      <button
        class="flex"
        :class="{
          'disable cursor-not-allowed': props.pageNumber === 1,
        }"
        @click="pagePreview"
      >
        <ChevronLeftIconComponent
          class="ml-1 h-4 stroke-gray-500"
          :stroke-color="'#626262'"
        />
      </button>
      <span>{{ props.pageNumber }} / {{ props.numberPageTotal }}</span>
      <button
        class="flex"
        :class="{
          'disable cursor-not-allowed': pageNumber === numberPageTotal,
        }"
        @click="pageNext"
      >
        <ChevronRightIconComponent class="ml-1 h-4" :stroke-color="'#626262'" />
      </button>
    </div>
    <div class="mr-2 items-center">{{ props.count }} produit(s)</div>
  </div>
</template>
<script lang="ts" setup>
import ChevronLeftIconComponent from '@/vuejs/modules/shared/icon/ChevronLeftIconComponent.vue'
import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/ChevronRightIconComponent.vue'

const emit = defineEmits(['pagePreview', 'pageNext'])

const props = defineProps({
  pageNumber: {
    type: Number,
    required: true,
  },
  count: {
    required: true,
    type: Number,
  },
  numberPageTotal: {
    required: true,
    type: Number,
  },
})

const pagePreview = () => {
  emit('pagePreview')
}

const pageNext = () => {
  emit('pageNext')
}
</script>

<style scoped></style>
