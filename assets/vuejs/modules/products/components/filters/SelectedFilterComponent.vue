<template>
  <div class="mr-2 flex w-full items-center">
    <span class="mr-2 flex"> {{ props.title }}: </span>
    <div class="flex justify-between rounded bg-primary p-1 text-sm text-white">
      <span v-if="props.property" class="mr-2">
        {{ props.property.label }} => {{ props.property.children.name }}
      </span>
      <span v-else class="mr-2">
        {{ props.label }}
      </span>
      <span class="cursor-pointer" @click="removeFilter">
        <span class="w-[16px] font-bold">X</span>
      </span>
    </div>
  </div>
</template>
<script lang="ts" setup>
const props = defineProps({
  label: {
    type: String,
    required: false,
    default: null,
  },
  title: {
    type: String,
    required: false,
    default: null,
  },
  property: {
    required: false,
    default: null,
  },
})

const emit = defineEmits(['remove-filter'])

const removeFilter = () => {
  emit('remove-filter')
}
</script>

<style scoped></style>
