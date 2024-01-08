<template>
  <h3 class="mt-10 mb-1 text-[23px] text-primary md:text-[35px]">
    Merci pour votre commande !
  </h3>
  <p class="mb-4 text-sm md:text-lg">
    Vous allez bientôt recevoir un email récapitulatif de votre commande à
    l'adresse suivante :
    <span class="text-primary">{{ user.email }}</span>
  </p>
  <RouterLink
    class="button button-primary mt-10 mb-10 w-full md:w-auto"
    :to="{ name: PageList.HOME_PAGE }"
  >
    <ArrowRightIconComponent class="mr-2 w-4 stroke-white" />
    Continuer vos achats
  </RouterLink>
</template>
<script lang="ts" setup>
import { storeToRefs } from 'pinia'

import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'

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
