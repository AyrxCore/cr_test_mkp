<template>
  <div
    class="absolute sticky bottom-1 flex w-full items-center border bg-white p-4 lg:relative lg:bottom-0 lg:border-0 lg:bg-transparent"
  >
    <div class="flex w-full flex-col">
      <div
        v-if="product"
        class="mb-2 flex flex-row items-center justify-between md:justify-evenly  lg:hidden"
      >
        <div class="flex items-center text-center justify-center">
          <span class="text-lg text-gray-500 line-through"
            >{{ product.priceReduce }}€ HT
          </span>
        </div>
        <div
          class="items-center justify-center flex text-[25px] font-bold text-primary"
        >
          {{ product.price }}€ HT
        </div>
        <div class="mt-3 ml-2 flex">
          <span class="ml-2 rounded-lg bg-purple-600 px-2.5 py-1.5 text-white text-sm md:text-lg"
            >{{ product.percent }}%</span
          >
        </div>
      </div>
      <div class="flex flex-row">
        <ButtonComponent class="button-gradient w-full lg:mt-14">
          <ShoppingCartIconComponent class="mr-2 w-4" /> Ajouter
        </ButtonComponent>
        <button class="ml-5 items-center rounded-lg border lg:hidden">
          <HeartIconComponent
            class="m-2 flex h-8 w-8 lg:hidden"
            :stroke-color="'#5E6875'"
          />
        </button>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'
import HeartIconComponent from '@/vuejs/modules/shared/icon/HeartIconComponent.vue'

const props = defineProps({
  product: {
    required: false,
    type: Object,
  },
})
</script>

<style scoped></style>
