<template>
  <h3 class="mb-1 mt-10 text-[23px] text-primary md:text-[35px]">
    Merci pour votre commande !
  </h3>
  <p class="mb-4 text-sm md:text-lg">
    Vous allez bientôt recevoir un email récapitulatif de votre commande à
    l'adresse suivante :
    <span class="text-primary">{{ user.email }}</span>
  </p>
  <RouterLink
    :to="{ name: PageList.HOME_PAGE }"
    class="button button-primary mb-10 mt-10 w-full md:w-auto"
  >
    <ArrowRightIconComponent class="mr-2 w-4 stroke-white" />
    Continuer vos achats
  </RouterLink>
</template>

<script lang="ts" setup>
import { storeToRefs } from 'pinia'

import { PageList } from '@/vuejs/router'
import { useUserStore } from '@/vuejs/stores/user'

import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'

const userStore = useUserStore()
const { user } = storeToRefs(userStore)
</script>
