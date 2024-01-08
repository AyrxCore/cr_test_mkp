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
