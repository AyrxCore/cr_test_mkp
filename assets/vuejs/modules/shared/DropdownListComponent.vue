<template>
  <div class="dropdown-select w-full">
    <button
      class="button-dropdown"
      @click='toggle = !toggle'
    >
      Mon compte
      <ChevronDownIconComponent :class="{'hidden': toggle}"/>
      <ChevronDownIconComponent class="rotate-180" :class="{'hidden': !toggle}"/>
    </button>
    <div
      class="dropdown-menu"
      :class="{
       'hidden': !toggle,
       '!flex': toggle
      }">
      <slot />
    </div>

  </div>
</template>

<script lang="ts" setup>
import ChevronDownIconComponent from '@/vuejs/modules/shared/icon/ChevronDownIconComponent.vue'
import { ref } from 'vue'
const toggle = ref<boolean>(false)

</script>

<style lang="postcss">
.button-dropdown {
  @apply bg-white text-primary flex flex-row justify-between p-2.5 rounded-lg items-center w-full md:w-1/2 lg:hidden;
}
.dropdown-select .dropdown-menu {
  @apply absolute hidden bg-white mt-1 px-5
  rounded-lg z-10
  overflow-auto
  shadow-xl
  h-[428px] flex-wrap
  text-left
  lg:h-auto
  lg:px-0 lg:mt-0 lg:overflow-auto
  lg:flex lg:flex-col lg:relative
  lg:bg-transparent  lg:shadow-none
  ;
}

.dropdown-select .dropdown-menu > div {

}
</style>
