<template>
  <div class="flex text-lg text-gray-500 rounded-lg bg-white p-2.5 mb-2.5">
    <div>
      <input type="checkbox" name="select_product" />
    </div>
    <div class="w-2/12 mx-auto">
      <img :src="product.img" alt="Image produit" class=" h-[116px] ml-2"/>
    </div>
    <div class="w-8/12">
      <h3 class="primary text-[22px] font-bold">{{ product.name }}</h3>
      <span class="text-gray-500 text-lg flex">Vendu par: {{ product.seller }}</span>
      <span class="text-gray-500 text-lg flex">Référence: {{ product.reference }}</span>
      <span class="text-green-400 mt-2 text-lg flex">En stock</span>
    </div>
    <div class="w-1/12 items-start">
      <input :value="product.qte" type="text" name="qte" class="w-14 rounded-lg text-center border border-gray-300"/>
    </div>
    <div class="w-1/12 grid gap-4 ">
      <div class="primary font-bold text-lg text-right">
        <span class="primary font-bold text-lg">{{ product.price_ht }}€ HT</span>
      </div>
      <div class="bottom-0 items-end flex justify-end">
        <button><EditIconComponent class="mr-2" /></button>
        <button><TrashIconComponent :stroke-color="'#9866ff'"/></button>
      </div>

    </div>
  </div>
</template>
<script lang="ts" setup>
import EditIconComponent from '@/vuejs/modules/shared/icon/EditIconComponent.vue'
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'
const props = defineProps({
  product: {
    required: true,
    type: Object,
  },
})

</script>
