<template>
  <svg
    :width="sizeWidth"
    :height="sizeHeight"
    viewBox="0 0 28 28"
    fill="none"
    :stroke="stroke"
    xmlns="http://www.w3.org/2000/svg"
  >
    <path
      d="M9 26.5C9.69036 26.5 10.25 25.9404 10.25 25.25C10.25 24.5596 9.69036 24 9 24C8.30964 24 7.75 24.5596 7.75 25.25C7.75 25.9404 8.30964 26.5 9 26.5Z"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
    <path
      d="M22.75 26.5C23.4404 26.5 24 25.9404 24 25.25C24 24.5596 23.4404 24 22.75 24C22.0596 24 21.5 24.5596 21.5 25.25C21.5 25.9404 22.0596 26.5 22.75 26.5Z"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
    <path
      d="M1.5625 1.5625H4.0625L7.3875 17.0875C7.50947 17.6561 7.82584 18.1643 8.28214 18.5248C8.73844 18.8853 9.30613 19.0754 9.8875 19.0625H22.1125C22.6815 19.0616 23.2331 18.8666 23.6763 18.5098C24.1195 18.153 24.4277 17.6557 24.55 17.1L26.6125 7.8125H5.4"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
  </svg>
</template>

<script lang="ts" setup>
const props = defineProps({
  stroke: {
    required: false,
    type: String,
    default: '#000000',
  },
  sizeWidth: {
    type: Number,
    default: 28,
  },
  sizeHeight: {
    type: Number,
    default: 28,
  },
})
</script>
