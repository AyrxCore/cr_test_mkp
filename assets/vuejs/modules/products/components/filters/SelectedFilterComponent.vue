<template>
  <div
    class="flex w-full items-center mr-2"
  >
    <span class="flex mr-2"> {{ props.title }}: </span>
    <div class="bg-primary flex text-white justify-between p-1 rounded text-sm">
      <span
        v-if="props.property"
        class="mr-2"
      >
        {{ props.property.label }} =>  {{ props.property.child.name }}
      </span>
      <span
        v-else
        class="mr-2"
      >
        {{ props.label }}
      </span>
      <span
        class="cursor-pointer"
        @click="removeFilter"
      > <TrashIconComponent class="w-[16px]"/> </span>
    </div>

  </div>
</template>
<script lang="ts" setup>

import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'

const props = defineProps({
  label: {
    required: false,
    default: null,
  },
  title: {
    required: false,
    default: null,
  },
  property: {
    required: false,
    default: null,
  }
})

const emit = defineEmits(['remove-filter'])

const removeFilter = (() =>  {
    emit('remove-filter')
})


</script>

<style scoped></style>
