<template>
  <BaseTemplate title="Contact | Qantis - MarketPlace" >

    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-5 sm:px-8">
      <breadcrumb-shared-component :current-page="'Contact'" />
      <div class="w-[100%] max-w-screen-2xl justify-end flex">
        <ContactUsButtonComponent />
      </div>
      <h3 class="text-primary text-title-35">Contactez-nous</h3>
      <span class="mb-5 text-sm md:text-base lg:text-lg text-gray-500">
        Notre Service adhérent est disponible du lundi au vendredi de 8h30 à 18h
      </span>
      <div
        class="m-auto my-2 mt-5 lg:mt-14 w-full flex flex-col lg:flex-row"
      >
        <!-- Bloc moyen de contact -->
        <div class="flex flex-col w-full lg:w-1/3 lg:border-r-2 lg:border-gray-300 lg:pr-[5.5rem]">
          <div class="mb-5 flex flex-col lg:h-[180px] rounded-lg bg-white py-2 lg:py-8 text-center">
            <PhoneIconComponent
              :stroke-color="'#9553FF'"
              class="mx-auto w-full"
            />
            <h4 class="text-primary mb-2 md:text-base lg:text-lg font-bold">Par téléphone</h4>
            <div class="flex justify-center">
              <span class="w-full md:text-base lg:text-lg text-gray-500">
              Appelez-nous au <a :href="`tel:${PHONE_ANIMATION}`">{{ PHONE_ANIMATION }}</a> <br />
              du lundi au vendredi de 8h30 à 18h
            </span>
            </div>

          </div>
          <div class="flex flex-col lg:h-[180px] rounded-lg bg-white py-2 lg:py-8 text-center">
            <MailIconComponent class="mx-auto mb-2 w-full text-secondary flex" />
            <h4 class="text-primary mb-2 font-bold flex justify-center">Par email</h4>
            <span class="w-full flex text-lg text-gray-500 justify-center">
              <a :href="`mailto:${MAIL_ANIMATION}`">{{ MAIL_ANIMATION }}</a>
            </span>
          </div>
        </div>
        <!-- Fin Bloc text actualité -->

        <!-- Bloc formulaire -->
        <FormContact />
        <!-- Fin Bloc formulaire -->
      </div>
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import PhoneIconComponent from '@/vuejs/modules/shared/icon/PhoneIconComponent.vue'
import MailIconComponent from '@/vuejs/modules/shared/icon/MailIconComponent.vue'
import { MAIL_ANIMATION, PHONE_ANIMATION } from '@/vuejs/services/const'
import FormContact from '@/vuejs/modules/contact/component/FormComponent.vue'
</script>

<style scoped></style>
