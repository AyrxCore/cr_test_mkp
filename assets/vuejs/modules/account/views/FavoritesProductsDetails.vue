<template>
  <AccountPage :selected-tab="tab">
    <template #right-side>
      <div class="flex justify-between">
        <h3 class="primary mb-2 text-[35px]">Nom de la liste</h3>
        <DefaultButtonComponent
          :btn-text-color="'text-purple-500'"
          :btn-color="'bg-transparent'"
          :rounded="'rounded-full'"
          class="border border-purple-600"
        >
          Renommer
        </DefaultButtonComponent>
      </div>

      <div class="items-center flex text-[16px] text-gray-500 mb-2.5">
        <div class="w-10/12">
          Description des articles
        </div>
        <div class="w-1/12">
          Qté
        </div>
        <div class="w-1/12">
          Sous-total
        </div>
      </div>
      <FavoritesProductsDetailsComponent v-for="(product, key) in listProducts" :key="key" :product="product" />
      <div class="flex justify-between mt-6">
        <DefaultButtonComponent
          :btn-text-color="'text-purple-500'"
          :btn-color="'bg-transparent'"
          :rounded="'rounded-full'"
          class="border border-purple-600"
        >
          Mettre à jour la liste
        </DefaultButtonComponent>
        <GradientButtonComponent
          type="submit"
          class="justify-center text-center py-1.5"
        >
          <ShoppingCartIconComponent
            :stroke-color="'#FFFFFF'"
            class="mr-2 w-4"
          />
          Ajouter au panier
        </GradientButtonComponent>
      </div>
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>

import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import { computed } from 'vue'
import productImage from '@/vuejs/assets/img/default-image.png'
import { getImage } from '@/vuejs/services/utils'
import FavoritesProductsDetailsComponent
  from '@/vuejs/modules/account/components/FavoriteProductDetailsComponent.vue'
import { AccountPageList } from '@/vuejs/modules/account/routerAccount'
import DefaultButtonComponent from '@/vuejs/modules/shared/DefaultButtonComponent.vue'
import GradientButtonComponent from '@/vuejs/modules/shared/GradientButtonComponent.vue'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'

const tab = computed(() => {
  return AccountPageList.FAVORIS_LIST
})

const productImageFile = getImage(productImage)

const listProducts = computed(() => {
  const products = []

  for (let i = 0; i < 3; i++) {
    const rndNb = Math.floor(Math.random() * 6) + 1
    products.push({
      name: 'Description du produit ou du service N°' + i,
      reference: 'XXXXXXXXXXX',
      seller: 'XXXXXXXXXX',
      qte: rndNb,
      price_ht: 'XX',
      img: productImageFile
    })
  }

  return products
})

</script>

<style scoped></style>
