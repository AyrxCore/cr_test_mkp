<template>
  <CartPage :selected-tab="tab">
    <template #left-side>
      <div class="">
        <h3 class="mb-1 text-[23px] text-primary md:text-[35px]">
          Merci pour votre commande !
        </h3>
        <p class="text-[19px] text-primary md:text-[25px]">
          Commande N° 2022120001
        </p>
        <p class="text-sm text-gray-500 md:text-lg">Créée le 12/12/2022</p>
        <p class="text-sm text-gray-500 md:text-lg">
          Vous allez bientôt recevoir un email récapitulatif de votre commande à
          l'adresse suivante :
          <span class="text-primary">qantis@email.com</span>
        </p>
        <RouterLink
          class="button button-gradient mt-10 w-full md:w-auto"
          :to="{ path: '/app/home' }"
        >
          <ArrowRightIconComponent :stroke-color="'#FFFFFF'" class="mr-2 w-4" />
          Continuer vos achats
        </RouterLink>

        <h3 class="mt-24 text-[19px] text-primary md:text-[25px]">
          <span class="text-gradient"> Depuis plus de 20 ans, </span>
          <br />
          <span> nous achetons mieux ensembles </span>
        </h3>
        <div class="container mx-auto">
          <div
            class="mt-5 grid grid-cols-2 gap-4 text-center md:grid-cols-4 md:text-left"
          >
            <div class="flex flex-col items-center md:items-start">
              <LayersIconComponent class="flex h-10 w-10" />
              <h4
                class="mt-3 flex text-base text-gray-500 md:mt-7 md:w-[176px] md:text-xl"
              >
                Réalisez 27% d'économies
              </h4>
              <p
                class="mt-3 flex text-sm text-gray-400 lg:w-[188px] md:text-base"
              >
                Bénéficiez d'avantages comparables à ceux des grands groupes
              </p>
            </div>
            <div
              class="flex w-[122px] flex-col items-center md:w-[154px] md:items-start"
            >
              <FolderSearchIconComponent class="flex h-10 w-10" />
              <h4 class="mt-3 flex text-base text-gray-500 md:mt-7 md:text-xl">
                Gagnez du temps
              </h4>
              <p class="mt-3 flex text-sm text-gray-400 md:text-base">
                Restez concentré sur votre coeur de métier
              </p>
            </div>
            <div class="flex flex-col items-center lg:w-[176px] md:items-start">
              <ExpandIconComponent class="flex h-10 w-10" />
              <h4 class="mt-3 flex text-base text-gray-500 md:mt-7 md:text-xl">
                Structurez et optimisez vos achats
              </h4>
              <p class="mt-3 text-sm text-gray-400 md:text-base">
                Déployez des accords-cadres déjà négociés, simplement
              </p>
            </div>
            <div class="flex flex-col items-center md:items-start">
              <LeafIconComponent class="flex h-10 w-10" />
              <h4
                class="mt-3 text-base text-gray-500 md:mt-7 md:w-[185px] md:text-xl"
              >
                Commencez votre démarche RSE par vos achats
              </h4>
              <p class="mt-3 text-sm text-gray-400 md:w-[176px] md:text-base">
                Nous notons nos fournisseurs à l'aide d'un référentiel RSE
              </p>
            </div>
          </div>
        </div>
      </div>
    </template>
    <template #right-side>
      <CartRightSideComponent class="md:mt-14" :show-next-button="false">
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
