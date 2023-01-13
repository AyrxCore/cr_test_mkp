<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-5 sm:px-8">
      <breadcrumb-shared-component
        :list-url="listUrl"
        :current-page="'Actualité'"
      />
      <div class="w-[100%] max-w-screen-2xl">
        <ContactUsButtonComponent />
      </div>
      <div
        class="m-auto my-2 flex w-[100%] max-w-screen-2xl flex-col lg:grid lg:grid-cols-2 lg:gap-4"
      >
        <!-- Bloc text actualité -->
        <div>
          <h3 class="primary text-title-35 mb-2">
            {{ contenusExpert[1].title }}
          </h3>
          <span
            class="mr-2 w-max rounded-md px-2 py-1 text-white text-white"
            :class="contenusExpert[1].categorie_color"
            >{{ contenusExpert[1].categorie_name }}</span
          >
          <span class="text-gray-500">{{ contenusExpert[1].date }}</span>
          <div class="mt-5 h-[auto] rounded-lg">
            <p class="whitespace-pre-line text-gray-500">
              {{ contenusExpert[1].description }}
            </p>
          </div>
          <a
            href="#"
            class="default-button-gradient mt-4 inline-flex justify-center px-3.5 py-3 text-center font-bold text-white"
          >
            <ArrowRigntIconComponent
              class="mt-1 mr-2 w-4 items-center"
              :stroke-color="'#FFFFFF'"
            />
            Découvrir ce partenaire
          </a>
        </div>
        <!-- Fin Bloc text actualité -->

        <!-- Bloc image -->
        <div class="mt-[7rem] h-[421px] rounded-lg bg-white">
          <img
            :src="contenusExpert[1].img_landscape"
            alt="Picture"
            class="m-auto h-[inherit] items-center"
          />
        </div>
        <!-- Fin Bloc image -->
      </div>

      <!-- Bloc articles recommandés -->
      <div class="mt-10 justify-center">
        <h3 class="primary home-subtitle mb-5">Articles recommandés</h3>
        <ContenusExpertComponent :contenus="contenusExpert" />
      </div>
      <!-- Fin Bloc articles recommandés -->
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import { ref } from 'vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ArrowRigntIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import { contenusExpert } from '@/vuejs/modules/actualites'
import ContenusExpertComponent from '@/vuejs/modules/home/component/ContenusExpertComponent.vue'

const listUrl = ref([
  {
    name: 'Actualités',
    url: '/app/actualites',
  },
])
</script>

<style scoped></style>
