<template>
  <AccountPage>
    <template #right-side>
      <div class="flex justify-between mt-2 md:mt-0">
        <h3 class="text-primary mb-2 page-principal-title">Nom de la liste</h3>
        <DefaultButtonComponent
          :btn-text-color="'text-purple-500'"
          :btn-color="'bg-transparent'"
          :rounded="'rounded-full'"
          class="border border-purple-600"
        >
          Renommer
        </DefaultButtonComponent>
      </div>

      <div class="mb-2.5 items-center text-sm lg:text-base text-gray-500 hidden md:flex">
        <div class="md:w-9/12 lg:w-10/12">Description des articles</div>
        <div class="w-1/12">Qté</div>
        <div class="flex justify-end md:w-2/12 lg:w-1/12">Sous-total</div>
      </div>
      <FavoritesProductsDetailsComponent
        v-for="(product, key) in listProducts"
        :key="key"
        :product="product"
      />
      <div class="mt-6 flex justify-between">
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
          class="justify-center py-1.5 text-center !px-2 md:!px-5"
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
import FavoritesProductsDetailsComponent from '@/vuejs/modules/account/components/FavoriteProductDetailsComponent.vue'
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
      image: productImageFile,
    })
  }

  return products
})
</script>

<style scoped></style>
