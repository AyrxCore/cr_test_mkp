<template>
  <div
    :class="[
      '[&_h1]:mb-4 [&_h2]:mb-4 [&_h3]:mb-3 [&_ol]:mb-3 [&_p]:mb-3 [&_ul]:mb-3 [&_ol]:list-decimal [&_ol]:pl-5',
      withCheckmarks
        ? 'with-checkmarks [&_ul]:list-none [&_ul]:pl-0'
        : '[&_ul]:list-disc [&_ul]:pl-5',
    ]"
    v-html="content"
  />
</template>

<script lang="ts" setup>
defineProps({
  content: {
    required: true,
    type: String,
  },
  withCheckmarks: {
    type: Boolean,
    default: false,
  },
})
</script>

<style scoped>
.with-checkmarks :deep(ul li) {
  position: relative;
  padding-left: 1.5rem;
}

.with-checkmarks :deep(ul li::before) {
  content: '';
  position: absolute;
  left: 0;
  top: 0.375rem;
  width: 0.875rem;
  height: 0.875rem;
  background-color: currentColor;
  mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'%3E%3C/polyline%3E%3C/svg%3E");
  mask-repeat: no-repeat;
  mask-position: center;
  mask-size: contain;
  -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'%3E%3C/polyline%3E%3C/svg%3E");
  -webkit-mask-repeat: no-repeat;
  -webkit-mask-position: center;
  -webkit-mask-size: contain;
}
</style>
