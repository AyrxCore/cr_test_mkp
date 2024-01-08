<template>
  <BaseTemplate>
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-5 sm:px-8">
      <breadcrumb-shared-component current-page="Page non trouvée" />
      <div class="m-auto my-2 w-[100%] max-w-screen-2xl">
        <div class="mt-10 flex">
          <div>
            <h3 class="text-title-primary mb-10 flex-col">
              <span class="flex">Erreur 404</span>
              <span class="flex font-bold">Page introuvable</span>
            </h3>
            <p class="w-1/2 text-sm md:text-lg">
              Nous sommes désolés, la page que vous cherchez n'a pas été
              trouvée. Nous vous proposons de retourner à la page d'accueil ou
              bien de découvrir toutes nos catégories d'achats.
            </p>
            <RouterLink
              class="button button-primary mt-10 mb-16 w-full md:w-auto"
              :to="{ name: PageList.HOME_PAGE }"
            >
              <ArrowRightIconComponent class="mr-2 w-4 stroke-white" />
              Retour à la page d'accueil
            </RouterLink>
          </div>
        </div>
      </div>
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import { PageList } from '@/vuejs/router'
</script>

<style scoped></style>
