<template>
  <div class="items-center mx-auto px-6 bg-white  py-2 rounded-md text-cotext h-full">
    <div class="justify-between items-center flex">
      <div class="h-9 w-20 bg-white"><img :src="props.product.imgFrn" alt="Image produit" /></div>
      <div class="border"></div>
      <HeartIconComponent />
    </div>
    <div class="items-center mx-auto ">
      <img :src="props.product.imgProduct" alt="Image produit" class="mx-auto h-[191px!important] w-[334px!important]"/>
    </div>
    <h3 class="text-left text-gray-600 font-bold text-lg text-cotext">Nom du produit</h3>
    <p class="text-left text-gray-400 mt-1 text-lg text-cotext">Description du produit ou du service</p>
    <div class="flex items-center mt-2">
      <span class="primary text-lg font-bold mr-2">85,50€</span>
      <span class="line-through text-gray-400 text-lg">100,00€ HT</span>
    </div>
    <div class="flex justify-between mt-5 mx-auto">
      <div>
        <span class="primary text-sm text-gray-300">Qté: </span>
        <select class="rounded-md border border-gray-300">
          <option
            v-for="i in 5" :key="i"
            value="{{i}}">
            {{ i }}
          </option>
        </select>
      </div>
      <DefaultButtonComponent type="submit" :is-loading="isLoading">
        <ShoppingCartIconComponent class="mr-2 w-4" /> Ajouter
      </DefaultButtonComponent>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { getImage } from '@/vuejs/services/utils'
import { ref } from 'vue'
import productImage from '@/vuejs/assets/img/default-image.png'
import DefaultButtonComponent from '@/vuejs/modules/shared/DefaultButtonComponent.vue'
import HeartIconComponent from '@/vuejs/modules/shared/icon/HeartIconComponent.vue';
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue';

const props = defineProps({
  product: {
    required: true,
    type: Object,
  },
})

const isLoading = ref<boolean>(false)
const productImageFile = getImage(productImage)

</script>

<style scoped></style>
