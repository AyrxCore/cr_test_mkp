<template>
  <CartPage :selected-tab="tab">
    <template #left-side>
      <div class="">
        <h3 class="primary mb-1 text-[35px]">Merci pour votre commande !</h3>
        <p class="primary text-[25px]">Commande N° 2022120001</p>
        <p class="text-lg text-gray-500">Créée le 12/12/2022</p>
        <p class="text-lg text-gray-500">
          Vous allez bientôt recevoir un email récapitulatif de votre commande à
          l'adresse suivante :
          <span class="primary">qantis@email.com</span>
        </p>
        <a class="mt-10 button button-gradient" href="/app/home">
          <ArrowRightIconComponent :stroke-color="'#FFFFFF'" class="mr-2 w-4" />
          Continuer vos achats
        </a>
        <h3 class="primary mt-24 text-[25px]">
          <span class="text-gradient"> Depuis plus de 20 ans, </span>
          <br />
          <span> nous achetons mieux ensembles </span>
        </h3>
        <div class="container mx-auto">
          <div
            class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4"
          >
            <div class="flex flex-col justify-center">
              <LayersIconComponent />
              <h4 class="mt-7 w-[176px] text-[20px] text-gray-500">
                Réalisez 27% d'économies
              </h4>
              <p class="mt-3 w-[188px] text-[16px] text-gray-400">
                Bénéficiez d'avantages comparables à ceux des grands groupes
              </p>
            </div>
            <div class="flex w-[154px] flex-col justify-center">
              <FolderSearchIconComponent />
              <h4 class="mt-7 text-[20px] text-gray-500">Gagnez du temps</h4>
              <p class="mt-3 text-[16px] text-gray-400">
                Restez concentré sur votre coeur de métier
              </p>
            </div>
            <div class="flex w-[176px] flex-col justify-center">
              <ExpandIconComponent />
              <h4 class="mt-7 text-[20px] text-gray-500">
                Structurez et optimisez vos achats
              </h4>
              <p class="mt-3 text-[16px] text-gray-400">
                Déployez des accords-cadres déjà négociés, simplement
              </p>
            </div>
            <div class="flex flex-col justify-center">
              <LeafIconComponent />
              <h4 class="mt-7 w-[185px] text-[20px] text-gray-500">
                Commencez votre démarche RSE par vos achats
              </h4>
              <p class="mt-3 w-[176px] text-[16px] text-gray-400">
                Nous notons nos fournisseurs à l'aide d'un référentiel RSE
              </p>
            </div>
          </div>
        </div>
      </div>
    </template>
    <template #right-side>
      <CartRightSideComponent class="mt-14" :show-next-button="false">
        <template #title> Récapitulatif </template>
        <template #button-label> Passer la commande </template>
      </CartRightSideComponent>
    </template>
  </CartPage>
</template>
<script lang="ts" setup>
import CartPage from '@/vuejs/modules/cart/pages/CartPage.vue'
import CartRightSideComponent from '@/vuejs/modules/cart/components/CartRightSideComponent.vue'
import { TAB_CONFIRMATION } from '@/vuejs/modules/cart'
import { computed } from 'vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import FolderSearchIconComponent from '@/vuejs/modules/shared/icon/FolderSearchIconComponent.vue'
import LayersIconComponent from '@/vuejs/modules/shared/icon/LayersIconComponent.vue'
import LeafIconComponent from '@/vuejs/modules/shared/icon/LeafIconComponent.vue'
import ExpandIconComponent from '@/vuejs/modules/shared/icon/ExpandIconComponent.vue'

const tab = computed(() => {
  return TAB_CONFIRMATION
})
</script>

<style scoped></style>
