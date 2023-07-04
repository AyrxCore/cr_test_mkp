<template>
  <h3 class="mt-10 mb-1 text-[23px] text-primary md:text-[35px]">
    Merci pour votre commande !
  </h3>
  <!-- <p class="text-[19px] text-primary md:text-[25px]">Commande N° 2022120001</p> -->
  <!-- <p class="text-sm text-gray-500 md:text-lg">Créée le 12/12/2022</p> -->
  <p class="text-sm text-gray-500 md:text-lg">
    Vous allez bientôt recevoir un email récapitulatif de votre commande à
    l'adresse suivante :
    <span class="text-primary">{{ user.email }}</span>
  </p>
  <RouterLink
    class="button button-gradient mt-10 w-full md:w-auto"
    :to="{ name: PageList.HOME_PAGE }"
  >
    <ArrowRightIconComponent :stroke-color="'#FFFFFF'" class="mr-2 w-4" />
    Continuer vos achats
  </RouterLink>

  <h3 class="mt-24 text-[19px] text-primary md:text-[25px]">
    <span class="text-gradient"> Depuis 2001, </span>
    <br />
    <span> nous achetons mieux ensemble </span>
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
        <p class="mt-3 flex text-sm text-gray-400 md:text-base lg:w-[188px]">
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
      <div class="flex flex-col items-center md:items-start lg:w-[176px]">
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
</template>
<script lang="ts" setup>
import { storeToRefs } from 'pinia'

import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import FolderSearchIconComponent from '@/vuejs/modules/shared/icon/FolderSearchIconComponent.vue'
import LayersIconComponent from '@/vuejs/modules/shared/icon/LayersIconComponent.vue'
import LeafIconComponent from '@/vuejs/modules/shared/icon/LeafIconComponent.vue'
import ExpandIconComponent from '@/vuejs/modules/shared/icon/ExpandIconComponent.vue'

import { useUserStore } from '@/vuejs/stores/user'
import { PageList } from '@/vuejs/router'
import { useCartStore } from '@/vuejs/stores/cart'
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { gtmCartTrackingEvent } from '@/vuejs/modules/cart'

const route = useRoute()
const userStore = useUserStore()
const { user } = storeToRefs(userStore)
const cartStore = useCartStore()

const cartResume = ref()

watch(
  () => route.params.id as string,
  async (id: string) => {
    if (id) {
      cartResume.value = await cartStore.findCartById(parseInt(id))
      await gtmCartTrackingEvent(
        'purchase',
        cartResume.value.cart,
        cartResume.value.confirmation,
      )
    }
  },

  { immediate: true },
)
</script>

<style scoped></style>
