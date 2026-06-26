<template>
  <h3 class="mb-1 mt-10 text-[23px] text-primary md:text-[35px]">
    Erreur lors de la finalisation de commande
  </h3>
  <div class="mb-10 flex w-1/2 bg-orange-100 p-3">
    <p class="text-sm text-orange-500 md:text-lg">
      Une erreur est survenue lors de la validation de votre commande. Aucun
      prélèvement n'a été effectué sur votre compte, veuillez contacter le
      service adhérents.
    </p>
  </div>
  <RouterLink
    :to="{ name: PageList.CONTACT_PAGE }"
    class="button button-primary mb-10"
  >
    <MailIconLightComponent class="mr-1" />
    Contactez-nous
  </RouterLink>
</template>

<script lang="ts" setup>
import { PageList } from '@/vuejs/router'

import MailIconLightComponent from '@/vuejs/modules/shared/icon/MailIconLightComponent.vue'
</script>
