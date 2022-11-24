<template>
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#9866ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
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
