<template>
  <div class="mb-2.5 flex rounded-lg bg-white p-2.5 text-lg text-gray-500 flex-col md:flex-row">
    <div class="flex md:w-9/12 lg:w-10/12">
      <div>
        <CheckboxComponent />
      </div>
      <div class="mx-auto w-5/12 md:w-2/12">
        <img :src="product.image" alt="Image produit" class="ml-2 w-full lg:ml-2 lg:h-[116px] lg:w-[190px]" />
      </div>
      <div class="w-6/12 md:w-8/12">
        <h3 class="text-lg font-bold text-primary lg:text-[22px]">{{ product.name }}</h3>
        <span
          class="flex flex-col text-sm text-gray-500 lg:text-lg"
        >
          <span>Vendu par: {{ product.seller }}</span>
          <span>Référence: {{ product.reference }}</span>
        </span>
        <span class="mt-2 flex text-sm text-green-400 lg:text-lg">En stock</span>
      </div>
    </div>
    <div class="flex md:w-3/12 lg:w-2/12">
      <div class="flex justify-center w-1/2 md:w-1/3 lg:w-1/2">
        <div class="flex items-center md:items-start ">
          <label class="md:hidden">Qté: </label>
          <input
            :value="product.qte"
            type="text"
            name="qte"
            class="w-12 lg:w-14 input-qte"
          />
        </div>
      </div>
      <div class="flex w-1/2 md:w-2/3 lg:w-1/2 md:justify-end ">
        <div class="flex flex-row items-center justify-between w-full md:w-auto md:flex-col">
          <span
            class="flex items-start text-primary text-sm md:text-base lg:text-lg font-bold"
          >
            {{ product.price_ht }}€ HT
          </span>
          <div class="flex items-start bottom-0 justify-between">
            <button><EditIconComponent class="mr-2" /></button>
            <button><TrashIconComponent :stroke-color="'#9866ff'" /></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import EditIconComponent from '@/vuejs/modules/shared/icon/EditIconComponent.vue'
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'
import CheckboxComponent from '@/vuejs/modules/shared/CheckboxComponent.vue'
const props = defineProps({
  product: {
    required: true,
    type: Object,
  },
})
</script>
<style scoped>
  .input-qte {
    @apply  rounded-lg border border-gray-300 text-center px-0 text-sm md:text-base lg:text-lg;
  }
</style>
