<template>
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="11" cy="11" r="8"></circle>
    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
  </svg>
</template>

<script lang="ts" setup></script>

<style lang="postcss">
.loader {
  @apply h-5 w-5 animate-spin;
  &.loader-xl {
    @apply h-14 w-14;
  }
}
</style>
