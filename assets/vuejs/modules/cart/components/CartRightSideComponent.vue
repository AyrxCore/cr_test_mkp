<template>
  <div class="p-5 bg-white rounded-lg">
    <h3 class="primary mb-5 text-[25px]">
      <slot name="title"/>
    </h3>
    <div class="justify-between text-gray-500 inline-flex mb-2 text-lg w-full">
      <div>Sous-total HT :</div>
      <div class="float-right">224,94 €</div>
    </div>
    <div class="justify-between text-gray-500 inline-flex text-lg w-full mb-2">
      <div>Frais de livraison HT :</div>
      <div class="float-right">12 €</div>
    </div>
    <div class="justify-between primary font-bold inline-flex mb-2 text-lg w-full">
      <div>TOTAL HT :</div>
      <div class="float-right">236,94 €</div>
    </div>
    <div class="justify-between text-gray-500 inline-flex text-lg w-full mb-2">
      <div>TVA :</div>
      <div class="float-right">47,39 €</div>
    </div>
    <div class="justify-between text-gray-500 inline-flex text-lg w-full">
      <div>TOTAL TTC :</div>
      <div class="float-right">284,33 €</div>
    </div>
    <a v-if="showNextButton" class="mt-3 button button-gradient w-full" :href="nextUrl">
      <ArrowRightIconComponent
        :stroke-color="'#FFFFFF'"
        class="mr-2 w-4"
      />
      <slot name="button-label"/>
    </a>
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
    default: true
  },
})
</script>
