<template>
  <div class="flex h-full flex-col rounded-md bg-primary p-8">
    <div
      class="loading mx-auto flex h-[250px] w-full items-center justify-center px-6 py-2"
    ></div>
    <p
      class="my-8 text-center text-sm font-normal uppercase text-white sm:text-lg"
    >
      Accord cadre
    </p>

    <a
      href="#"
      class="button button-white button-white-primary flex justify-center hover:!text-white"
    >
      <ArrowRightIconComponent />
      Découvrir l'accord-cadre
    </a>
  </div>
</template>
<script lang="ts" setup>
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
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
