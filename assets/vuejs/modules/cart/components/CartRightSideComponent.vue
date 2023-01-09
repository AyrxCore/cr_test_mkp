<template>
  <div class="rounded-lg bg-white p-5 lg:p-3 xl:p-5 flex flex-col md:flex-row lg:flex-col justify-between items-center">
    <div class="w-auto md:w-7/12 lg:w-auto">
      <h3 class="text-primary mb-5 text-[19px] xl:text-[25px]">
        <slot name="title" />
      </h3>
      <div class="mb-2 inline-flex w-full justify-between text-sm md:text-base xl:text-lg text-gray-500">
        <div>Sous-total HT :</div>
        <div class="float-right">224,94 €</div>
      </div>
      <div class="mb-2 inline-flex w-full justify-between text-sm md:text-base xl:text-lg text-gray-500">
        <div>Frais de livraison HT :</div>
        <div class="float-right">12 €</div>
      </div>
      <div
        class="primary mb-2 inline-flex w-full justify-between text-sm md:text-base xl:text-lg font-bold"
      >
        <div>TOTAL HT :</div>
        <div class="float-right">236,94 €</div>
      </div>
      <div class="mb-2 inline-flex w-full justify-between text-sm md:text-base xl:text-lg text-gray-500">
        <div>TVA :</div>
        <div class="float-right">47,39 €</div>
      </div>
      <div class="inline-flex w-full justify-between text-sm md:text-base xl:text-lg text-gray-500">
        <div>TOTAL TTC :</div>
        <div class="float-right">284,33 €</div>
      </div>
    </div>
    <div class="w-full md:w-4/12 xl:w-full lg:w-auto lg:items-center lg:justify-center lg:flex">
      <RouterLink
        v-if="showNextButton"
        class="button button-gradient mt-3 w-full"
        :to="{ path: nextUrl }"
      >
        <ArrowRightIconComponent :stroke-color="'#FFFFFF'" class="mr-2 w-4" />
        <slot name="button-label" />
      </RouterLink>
    </div>
  </div>
</template>
<script lang="ts" setup>
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'

const props = defineProps({
  nextUrl: {
    required: true,
    type: String,
  },
  showNextButton: {
    required: false,
    type: Boolean,
    default: true,
  },
})
</script>
