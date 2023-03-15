<template>
  <AccountPage>
    <template #right-side>
      <h3 class="mb-2 mt-2 text-title-35 text-primary md:mt-0">
        Mes produits favoris
      </h3>
      <div
        class="mt-10 mb-2.5 hidden items-center px-2.5 text-sm text-gray-500 md:flex lg:text-base"
      >
        <div class="w-5/12">Nom de la liste</div>
        <div class="w-2/12">Créée le</div>
        <div class="w-2/12">Modifiée le</div>
        <div class="w-2/12">Nombre d'articles</div>
        <div class="w-1/12"></div>
      </div>
      <div class="flex flex-row flex-wrap justify-between">
        <FavoritesProductsComponent
          v-for="(favorite, key) in listFavorites"
          :key="key"
          :favorite="favorite"
        />
      </div>
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>
import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import { computed, ref } from 'vue'
import FavoritesProductsComponent from '@/vuejs/modules/account/components/FavoriteProductComponent.vue'
import { AccountPageList } from '@/vuejs/router/pages-list'

const tab = computed(() => {
  return AccountPageList.FAVORIS_LIST
})

const listFavorites = computed(() => {
  const articles = []
  const nomListe = ['Bureau', 'Véhicules', 'Autres']
  const dateCreate = ['01/01/2022', '07/04/2022', '19/08/2022']
  const dateUpdate = ['09/01/2022', '22/04/2022', '04/09/2022']

  for (let i = 0; i < 3; i++) {
    const rndNb = Math.floor(Math.random() * 6) + 1

    articles.push({
      name: nomListe[i],
      dateCreate: dateCreate[i],
      dateUpdate: dateUpdate[i],
      numberArticle: rndNb,
    })
  }

  return articles
})
</script>

<style scoped></style>
