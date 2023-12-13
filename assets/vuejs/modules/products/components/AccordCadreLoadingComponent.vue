<template>
  <div class="flex h-full flex-col rounded-md px-6 pb-8 pt-5">
    <div
      class="loading mx-auto flex h-[250px] w-full items-center justify-center px-6 py-2"
    ></div>
    <p class="mb-8 mt-6 text-center text-sm font-normal uppercase sm:text-lg">
      Accord cadre
    </p>

    <a
      href="#"
      class="button button-primary flex justify-center"
      :style="{
        color: betterTextColor('primary'),
      }"
    >
      Profiter de l'accord-cadre
    </a>
  </div>
</template>
<script lang="ts" setup>
import { betterTextColor } from '@/vuejs/services/utils'
</script>

<style scoped>
.loading {
  background: linear-gradient(-90deg, #e8e8e8, #d0cfcf, #e8e8e8);
  background-size: 300% 300%;
  animation: gradient 1s ease infinite;
}

@keyframes gradient {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}
</style>
