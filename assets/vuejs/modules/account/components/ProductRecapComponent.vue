<template>
  <div class="mx-auto h-full items-center bg-white">
    <div class="mb-1 flex w-full">
      <div class="ml-2 w-[2%]">
        <input type="checkbox" name="select_product" />
      </div>
      <div class="w-[13%]">
        <img
          :src="product.img"
          alt="Image produit"
          class="ml-2 h-[116px] w-[190px]"
        />
      </div>
      <div class="ml-2 w-[45%]">
        <h3 class="primary text-[22px] font-bold">{{ product.name }}</h3>
        <span class="text-lg text-gray-500">
          Vendu par: {{ product.seller }}
        </span>
        <br />
        <span class="text-lg text-gray-500">
          Référence: {{ product.reference }}
        </span>
        <br />
        <span class="mt-2 text-lg text-green-400">En stock</span>
      </div>
      <div class="w-[10%]">
        <input
          :value="product.qte"
          type="text"
          name="qte"
          class="w-14 rounded-lg border border-gray-300 text-center"
        />
      </div>
      <div class="mr-3 w-[10%] text-center">
        <span class="mt-2 text-lg text-gray-400"
          >{{ product.price_ht }}€ HT</span
        >
      </div>
      <div class="w-[10%] text-center">
        <span class="primary mt-2 text-lg font-bold"
          >{{ product.price_ttc }}€ HT</span
        >
      </div>
      <div class="float-right inline-flex flex-1">
        <a href="#">
          <HeartIconComponent :stroke-color="'#5E6875'" class="mr-2" />
        </a>
        <a href="#">
          <TrashIconComponent :stroke-color="'#5E6875'" />
        </a>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import HeartIconComponent from '@/vuejs/modules/shared/icon/HeartIconComponent.vue'
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'

const props = defineProps({
  product: {
    required: true,
    type: Object,
  },
})
</script>
