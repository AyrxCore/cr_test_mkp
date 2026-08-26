<template>
  <div class="dropdown-select w-full">
    <button class="button-dropdown" @click="toggle = !toggle">
      <slot name="button-label" />
      <ChevronDownIconComponent :class="{ hidden: toggle }" />
      <ChevronDownIconComponent
        class="rotate-180"
        :class="{ hidden: !toggle }"
      />
    </button>
    <div
      class="dropdown-menu"
      :class="{
        hidden: !toggle,
        'flex!': toggle,
      }"
    >
      <slot name="content" />
    </div>
  </div>
</template>

<script lang="ts" setup>
import ChevronDownIconComponent from '@/vuejs/modules/shared/icon/ChevronDownIconComponent.vue'
import { ref } from 'vue'
const toggle = ref<boolean>(false)
</script>

<style>
@reference '@/style/main.css';

.button-dropdown {
  @apply flex w-full flex-row items-center justify-between rounded-lg bg-white p-2.5 text-primary lg:w-1/2 xl:hidden;
}
.dropdown-select .dropdown-menu {
  @apply absolute z-10 mt-1 hidden
  flex-wrap overflow-auto
  rounded-lg
  bg-white
  text-left
  shadow-xl
  xl:relative
  xl:mt-0 xl:flex xl:h-auto
  xl:flex-col xl:overflow-auto xl:bg-transparent
  xl:px-0  xl:shadow-none;
}
</style>
