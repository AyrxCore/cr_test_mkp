<template>
  <CartPage :selected-tab="tab">
    <template #left-side>
      <div class="text-cotext">
        <h3 class="primary mb-1 text-[35px]">Merci pour votre commande !</h3>
        <p class="primary text-[25px]">Commande N° XXX</p>
        <p class="text-gray-500 text-lg">Créée le 01/01/01</p>
        <p class="text-gray-500 text-lg">Vous allez bientôt recevoir un email récapitulatif de votre commande à l'adresse suivante :
          <span class="primary">adresse@email.com</span>
        </p>
        <GradientLinkComponent
          class="mt-10 justify-center"
          :href="'#'"
        >
          <ArrowRightIconComponent
            :stroke-color="'#FFFFFF'"
            class="mr-2 w-4"
          />
          Continuer vos achats
        </GradientLinkComponent>
        <h3 class="text-[25px] primary mt-24">
          <span class="bg-gradient-to-r from-purple-500 via-blue-500 to-cyan-500 bg-clip-text text-transparent">
            Depuis plus de 20 ans,
          </span>
          <br />
          <span>
            nous achetons mieux ensembles
          </span>
        </h3>
        <div class="container mx-auto">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4 mt-5">
            <div class="flex flex-col justify-center">
              <LayersIconComponent />
              <h4 class="text-[20px] w-[176px] text-gray-500 mt-7">
                Réalisez 27% d'économies
              </h4>
              <p class="text-[16px] w-[188px] text-gray-400 mt-3">
                Bénéficiez d'avantages comparables à ceux des grands groupes
              </p>
            </div>
            <div class="w-[154px] flex flex-col justify-center">
              <FolderSearchIconComponent />
              <h4 class="text-[20px] text-gray-500 mt-7">
                Gagnez du temps
              </h4>
              <p class="text-[16px] text-gray-400 mt-3">
                Restez concentré sur votre coeur de métier
              </p>
            </div>
            <div class="w-[176px] flex flex-col justify-center">
              <ExpandIconComponent />
              <h4 class="text-[20px] text-gray-500 mt-7">
                Structurez et optimisez vos achats
              </h4>
              <p class="text-[16px] text-gray-400 mt-3">
                Déployez des accords-cadres déjà négociés, simplement
              </p>
            </div>
            <div class="flex flex-col justify-center">
              <LeafIconComponent />
              <h4 class="text-[20px] w-[185px] text-gray-500 mt-7">
                Commencez votre démarche RSE par vos achats
              </h4>
              <p class="text-[16px] text-gray-400 mt-3 w-[176px]">
                Nous notons nos fournisseurs à l'aide d'un référentiel RSE
              </p>
            </div>
          </div>
        </div>
      </div>
    </template>
    <template #right-side>
      <CartRightSideComponent class="mt-14">
        <template #title>
          Récapitulatif
        </template>
        <template #button-label>
          Passer la commande
        </template>
      </CartRightSideComponent>
    </template>
  </CartPage>
</template>
<script lang="ts" setup>

import CartPage from '@/vuejs/modules/cart/pages/CartPage.vue'
import DefaultButtonComponent from '@/vuejs/modules/shared/DefaultButtonComponent.vue'
import CartRightSideComponent from '@/vuejs/modules/cart/components/CartRightSideComponent.vue'
import {TAB_CONFIRMATION} from '@/vuejs/modules/cart'
import { computed } from 'vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import GradientLinkComponent from '@/vuejs/modules/shared/GradientLinkComponent.vue'
import EconomyIconComponent from '@/vuejs/modules/shared/icon/EconomyIconComponent.vue';
import FolderSearchIconComponent from '@/vuejs/modules/shared/icon/FolderSearchIconComponent.vue';
import LayersIconComponent from '@/vuejs/modules/shared/icon/LayersIconComponent.vue';
import LeafIconComponent from '@/vuejs/modules/shared/icon/LeafIconComponent.vue';
import ExpandIconComponent from '@/vuejs/modules/shared/icon/ExpandIconComponent.vue';

const tab = computed(() => {
  return TAB_CONFIRMATION
})
</script>

<style scoped></style>
