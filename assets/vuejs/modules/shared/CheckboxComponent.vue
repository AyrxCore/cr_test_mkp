<template>
  <div>
    <label
      v-if="props.positionBefore"
      :for="props.id"
      class="ml-2 block text-sm text-gray-900"
    >
      <slot name="label-before" />
    </label>
    <input
      :id="props.id"
      type="checkbox"
      class="appearance-none rounded border border-gray-400 text-secondary checked:bg-secondary focus:ring-secondary"
    />
    <label
      v-if="props.positionAfter"
      :for="props.id"
      class="ml-2 block text-sm text-gray-900"
    >
      <slot name="label-after" />
    </label>
  </div>
</template>

<script lang="ts" setup>
const props = defineProps({
  id: {
    required: false,
    type: String,
    default: '',
  },
  positionBefore: {
    required: false,
    type: Boolean,
    default: false,
  },
  positionAfter: {
    required: false,
    type: Boolean,
    default: false,
  },
})
</script>
