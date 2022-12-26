<template>
  <div class="mx-auto h-full items-center bg-white">
    <div class="mb-1 flex w-full flex-col lg:flex-row">
      <div class="flex lg:w-7/12">
        <div class="lg:ml-2">
          <CheckboxComponent />
        </div>
        <div class="w-5/12 lg:w-4/12">
          <img
            :src="product.image"
            alt="Image produit"
            class="w-full lg:ml-2 lg:h-[116px] lg:w-[190px]"
          />
        </div>
        <div class="w-6/12 lg:ml-2 lg:w-7/12">
          <h3 class="text-lg font-bold text-primary lg:text-[22px]">
            {{ product.name }}
          </h3>
          <span
            class="flex flex-col text-sm text-gray-500 md:flex-row lg:text-lg"
          >
            <span>Vendu par: </span>
            <span>{{ product.partner }}</span>
          </span>
          <span
            class="flex flex-col text-sm text-gray-500 lg:flex-row lg:text-lg"
          >
            <span>Référence: </span>
            <span>{{ product.reference }}</span>
          </span>
          <span class="mt-2 flex text-sm text-green-400 lg:text-lg"
            >En stock</span
          >
        </div>
      </div>
      <div class="flex w-full items-center justify-between md:w-1/2 lg:w-5/12 md:float-right lg:float-none">
        <div class="text-center lg:mr-5 lg:w-2/12">
          <input
            :value="1"
            type="text"
            name="qte"
            class="w-10 rounded-lg border border-gray-300 text-center lg:w-14"
          />
        </div>
        <div class="lg:w-4/12">
          <span class="mt-2 text-sm text-gray-400 lg:text-lg">
            {{ product.price }}€ HT
          </span>
        </div>
        <div class="lg:w-4/12">
          <span class="mt-2 text-sm font-bold text-primary lg:text-lg">
            {{ product.price }}€ HT
          </span>
        </div>
        <button class="flex text-gray-500">
          <HeartIconComponent class="mr-2 stroke-gray-500" />
        </button>
        <button class="flex text-gray-500">
          <TrashIconComponent :stroke-color="'#5E6875'" />
        </button>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import HeartIconComponent from '@/vuejs/modules/shared/icon/HeartIconComponent.vue'
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'
import CheckboxComponent from '@/vuejs/modules/shared/CheckboxComponent.vue'

const props = defineProps({
  product: {
    required: true,
    type: Object,
  },
})
</script>
