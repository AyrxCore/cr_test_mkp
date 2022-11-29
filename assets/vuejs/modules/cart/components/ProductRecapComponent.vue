<template>
  <div
    class="text-cotext mx-auto h-full items-center bg-white "
  >
    <div class="flex mb-1 w-full">
      <div class="w-[2%] ml-2">
        <input type="checkbox" name="select_product" />
      </div>
      <div class="w-[13%]">
        <img :src="product.img" alt="Image produit" class=" h-[116px] w-[190px] ml-2"/>
      </div>
      <div class="w-[45%] ml-2">
        <h3 class="primary text-[22px] font-bold">{{ product.name }}</h3>
        <span class="text-gray-500 text-lg">Vendu par: {{ product.seller }}</span>
        <br />
        <span class="text-gray-500 text-lg">Référence: {{ product.reference }}</span>
        <br />
        <span class="text-green-400 mt-2 text-lg">En stock</span>
      </div>
      <div class="w-[10%]">
        <input :value="product.qte" type="text" name="qte" class="w-14 rounded-lg text-center border border-gray-300"/>
      </div>
      <div class="w-[10%] mr-3 text-center">
        <span class="text-gray-400 mt-2 text-lg">{{ product.price_ht }}€ HT</span>
      </div>
      <div class="w-[10%] text-center">
        <span class="primary font-bold text-lg mt-2">{{ product.price_ttc }}€ HT</span>
      </div>
      <div class="inline-flex flex-1 float-right">
        <a href="#"> <HeartIconComponent :stroke-color="'#5E6875'" class="mr-2" /></a>
        <a href="#"> <TrashIconComponent :stroke-color="'#5E6875'" /></a>
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
