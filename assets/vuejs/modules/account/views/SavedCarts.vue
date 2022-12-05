<template>
  <AccountPage :selected-tab="tab">
    <template #right-side>
      <h3 class="primary mb-2 text-[35px]">Panier sauvegardés</h3>
      <div class="items-center flex text-lg text-gray-500 mb-2.5">
        <div class="w-3/12">
          Nom du panier
        </div>
        <div class="w-2/12">
          Créée le
        </div>
        <div class="w-3/12">
          Nombre d'articles
        </div>
        <div class="w-2/12">
          Total
        </div>
        <div class="w-2/12"></div>
      </div>
      <SavedCartComponent v-for="(cart, key) in listSavedCarts" :key="key" :cart="cart" />
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>

import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import { computed, ref } from 'vue'
import FavoritesProductsComponent from '@/vuejs/modules/account/components/FavoriteProductComponent.vue'
import { AccountPageList } from '@/vuejs/modules/account/routerAccount'
import SavedCartComponent from '@/vuejs/modules/account/components/SavedCartComponent.vue';

const tab = computed(() => {
  return AccountPageList.SAVED_CARTS
})

const listSavedCarts = computed(() => {
  const carts = []
  const dateCreate = ['01/01/2022', '07/04/2022', '19/08/2022']

  for (let i = 0; i < 3; i++) {
    const rndNb = Math.floor(Math.random() * 6) + 1
    carts.push({
      name: 'Panier N° ' + (i+1),
      dateCreate: dateCreate[i],
      total: 'XX,XX',
      numberArticle: rndNb,
    })
  }

  return carts
})

</script>

<style scoped></style>
