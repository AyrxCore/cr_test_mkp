<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-5 sm:px-8">
      <breadcrumb-shared-component :current-page="'Page non trouvée'" />
      <div class="w-[100%] max-w-screen-2xl">
        <ContactUsButtonComponent />
      </div>
      <div class="m-auto my-2 w-[100%] max-w-screen-2xl">
        <div
          class="mt-10 flex"
        >
          <div class="">
            <h3 class="mb-1 text-title-35 text-primary flex-col">
              <span class="flex">Erreur 404</span>
              <span class="font-bold flex">Page introuvable</span>
            </h3>
            <p class="text-sm text-gray-500 md:text-lg w-1/2">
              Nous sommes désolés, la page que vous cherchez n'a pas été trouvée. Nous vous proposons de retourner à la page d'accueil ou bien de découvrir toutes nos catégories d'achats.
            </p>
            <RouterLink
              class="button button-gradient mt-10 w-full md:w-auto"
              :to="{ name: PageList.HOME_PAGE }"
            >
              <ArrowRightIconComponent :stroke-color="'#FFFFFF'" class="mr-2 w-4" />
              Retour à la page d'accueil
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
        </div>
      </div>
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import LeafIconComponent from '@/vuejs/modules/shared/icon/LeafIconComponent.vue'
import ExpandIconComponent from '@/vuejs/modules/shared/icon/ExpandIconComponent.vue'
import FolderSearchIconComponent from '@/vuejs/modules/shared/icon/FolderSearchIconComponent.vue'
import LayersIconComponent from '@/vuejs/modules/shared/icon/LayersIconComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import { PageList } from '@/vuejs/router'
</script>

<style scoped></style>
