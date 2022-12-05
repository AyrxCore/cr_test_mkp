<template>
  <svg
    :width="sizeWidth"
    :height="sizeHeight"
    viewBox="0 0 15 14"
    :fill="fillColor"
    xmlns="http://www.w3.org/2000/svg">
    <path d="M1 6.99998L7.48 0.519979M14.5 6.99998L1 6.99998L14.5 6.99998ZM1 6.99998L7.48 13.48L1 6.99998Z" :stroke="strokeColor" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>

</template>

<script lang="ts" setup>
const props = defineProps({
  fillColor: {
    type: String,
    default: 'none'
  },
  strokeColor: {
    type: String,
    default: '#9553FF'
  },
  sizeHeight: {
    type: Number,
    default: 14
  },
  sizeWidth: {
    type: Number,
    default: 15
  }
})
</script>
