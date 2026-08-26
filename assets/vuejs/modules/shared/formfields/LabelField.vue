<template>
  <label class="pr-4 font-cotext font-bold" :class="props.classes">
    {{ props.title }}
  </label>
</template>

<script lang="ts" setup>
const props = defineProps({
  title: {
    required: true,
    type: String,
  },
  classes: {
    required: false,
    type: String,
    default: '',
  },
})
</script>
