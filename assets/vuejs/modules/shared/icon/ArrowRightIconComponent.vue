<template>
  <svg
    :width="sizeWidth"
    :height="sizeHeight"
    viewBox="0 0 15 14"
    :fill="fillColor"
    xmlns="http://www.w3.org/2000/svg"
  >
    <path
      d="M14.5 7.00002L8.02 13.48M1 7.00002H14.5H1ZM14.5 7.00002L8.02 0.52002L14.5 7.00002Z"
      :stroke="strokeColor"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
  </svg>
</template>

<script lang="ts" setup>
const props = defineProps({
  fillColor: {
    type: String,
    default: 'none',
  },
  strokeColor: {
    type: String,
    default: '#9553FF',
  },
  sizeHeight: {
    type: Number,
    default: 14,
  },
  sizeWidth: {
    type: Number,
    default: 15,
  },
})
</script>
