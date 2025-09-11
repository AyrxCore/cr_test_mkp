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
import { ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useRoute } from 'vue-router'

import { PageList } from '@/vuejs/router'
import { useCartStore } from '@/vuejs/stores/cart'
import { useUserStore } from '@/vuejs/stores/user'
import { formatCartItemsGtmEvent, sendGtmEvent } from '@/vuejs/services/gtm'

import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'

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
      sendGtmEvent('purchase', {
        ecommerce: {
          currency: 'EUR',
          items: formatCartItemsGtmEvent(cartResume.value),
        },
      })
    }
  },

  { immediate: true },
)
</script>
