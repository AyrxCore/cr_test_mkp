<template>
  <BaseTemplate title="Contact | Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-5 sm:px-8">
      <BreadcrumbSharedComponent :current-page="'Contact'" />
      <div class="flex w-[100%] max-w-screen-2xl justify-end">
        <ContactUsButtonComponent />
      </div>
      <h3 class="text-title-35 text-primary">Contactez-nous</h3>
      <span class="mb-5 text-sm text-gray-500 md:text-base lg:text-lg">
        Notre Service adhérent est disponible du lundi au vendredi de 8h30 à 18h
      </span>
      <div class="m-auto my-2 mt-5 flex w-full flex-col lg:mt-14 lg:flex-row">
        <!-- Bloc moyen de contact -->
        <div
          class="flex w-full flex-col lg:w-1/3 lg:border-r-2 lg:border-gray-300 lg:pr-[5.5rem]"
        >
          <div
            class="mb-5 flex flex-col rounded-lg bg-white py-2 text-center lg:h-[180px] lg:py-8"
          >
            <PhoneIconComponent
              :stroke-color="'#9553FF'"
              class="mx-auto w-full"
            />
            <h4 class="mb-2 font-bold text-primary md:text-base lg:text-lg">
              Par téléphone
            </h4>
            <div class="flex justify-center">
              <span class="w-full text-gray-500 md:text-base lg:text-lg">
                Appelez-nous au
                <a :href="`tel:${PHONE_ANIMATION}`">{{ PHONE_ANIMATION }}</a>
                <br />
                du lundi au vendredi de 8h30 à 18h
              </span>
            </div>
          </div>
          <div
            class="flex flex-col rounded-lg bg-white py-2 text-center lg:h-[180px] lg:py-8"
          >
            <MailIconComponent
              class="mx-auto mb-2 flex w-full text-secondary"
            />
            <h4 class="mb-2 flex justify-center font-bold text-primary">
              Par email
            </h4>
            <span class="flex w-full justify-center text-lg text-gray-500">
              <a :href="`mailto:${MAIL_ANIMATION}`">{{ MAIL_ANIMATION }}</a>
            </span>
          </div>
        </div>
        <!-- Fin Bloc text actualité -->

        <!-- Bloc formulaire -->
        <div
          class="mt-5 w-full rounded-lg text-center lg:right-0 lg:mt-0 lg:w-2/3 lg:pl-[5.5rem]"
        >
          <FormContact />
        </div>
        <!-- Fin Bloc formulaire -->
      </div>
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import FormContact from '@/vuejs/modules/contact/component/FormComponent.vue'
import MailIconComponent from '@/vuejs/modules/shared/icon/MailIconComponent.vue'
import PhoneIconComponent from '@/vuejs/modules/shared/icon/PhoneIconComponent.vue'

import { MAIL_ANIMATION, PHONE_ANIMATION } from '@/vuejs/services/const'
</script>

<style scoped></style>
