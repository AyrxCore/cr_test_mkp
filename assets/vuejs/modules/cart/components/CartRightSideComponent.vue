<template>
  <div class="p-5 bg-white rounded-lg">
    <h3 class="primary mb-5 text-[25px]">
      <slot name="title"/>
    </h3>
    <div class="justify-between text-gray-500 inline-flex mb-2 text-lg w-full">
      <div>Sous-total HT :</div>
      <div class="float-right">XX €</div>
    </div>
    <div class="justify-between text-gray-500 inline-flex text-lg w-full mb-2">
      <div>Frais de livraison HT :</div>
      <div class="float-right">XX €</div>
    </div>
    <div class="justify-between primary font-bold inline-flex mb-2 text-lg w-full">
      <div>TOTAL HT :</div>
      <div class="float-right">XX €</div>
    </div>
    <div class="justify-between text-gray-500 inline-flex text-lg w-full mb-2">
      <div>TVA :</div>
      <div class="float-right">XX €</div>
    </div>
    <div class="justify-between text-gray-500 inline-flex text-lg w-full">
      <div>TOTAL TTC :</div>
      <div class="float-right">XX €</div>
    </div>
    <GradientButtonComponent
      type="submit"
      class="mt-5 justify-center text-center"
    >
      <ArrowRightIconComponent
        :stroke-color="'#FFFFFF'"
        class="mr-2 w-4"
      />
      <slot name="button-label"/>
    </GradientButtonComponent>
  </div>
</template>
<script lang="ts" setup>
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import GradientButtonComponent from '@/vuejs/modules/shared/GradientButtonComponent.vue'

</script>
