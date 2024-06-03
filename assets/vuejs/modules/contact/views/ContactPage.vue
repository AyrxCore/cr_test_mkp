<template>
  <BaseTemplate title="Contact">
    <div
      class="xs:w-[100%] m-auto mt-4 mb-24 max-w-screen-2xl flex-1 px-5 sm:px-8"
    >
      <BreadcrumbSharedComponent
        current-page="Contact"
        gtm-event-name="click_contact_breadcrumbs"
      />
      <h3 class="text-title-34 my-4 font-bold text-primary">Contactez-nous</h3>
      <span class="mb-5 text-lg">
        {{ customerService }} est disponible du lundi au vendredi de 8h30 à 18h
      </span>
      <div class="m-auto my-2 mt-5 flex w-full flex-col lg:mt-14 lg:flex-row">
        <!-- Bloc moyen de contact -->
        <div
          class="flex w-full flex-col lg:w-1/3 lg:border-r-2 lg:border-gray-300 lg:pr-[5.5rem]"
        >
          <div
            class="mb-5 flex flex-col rounded-lg bg-white py-6 text-center lg:py-8"
          >
            <PhoneIconComponent
              class="mx-auto mb-4 w-full stroke-secondary"
              :width="40"
              :height="40"
            />
            <h4 class="mb-2 text-2xl font-bold text-primary">Par téléphone</h4>
            <div class="flex justify-center">
              <span class="w-full text-lg">
                Appelez-nous au
                <a
                  class="underline"
                  :href="`tel:${channel.phoneNumber}`"
                  @click="sendGaEvent('click_contact_tel')"
                  >{{ formattedPhoneNumber }}</a
                >
                <br />
                du lundi au vendredi de 8h30 à 18h
              </span>
            </div>
          </div>
          <div
            class="mb-5 flex flex-col rounded-lg bg-white py-6 text-center lg:h-[180px] lg:py-8"
          >
            <MailIconComponent
              class="mx-auto mb-4 w-full stroke-secondary text-secondary"
              :width="40"
              :height="40"
            />
            <h4 class="mb-2 text-2xl font-bold text-primary">Par email</h4>
            <div class="flex justify-center">
              <span class="flex w-full justify-center text-lg">
                à
                <RouterLink
                  to="#topFormContact"
                  class="ml-1 underline"
                  @click="sendGaEvent('click_contact_mail')"
                  >{{ channel.email }}</RouterLink
                >
              </span>
            </div>
          </div>
        </div>
        <!-- Fin Bloc text actualité -->

        <!-- Bloc formulaire -->
        <div
          id="topFormContact"
          class="anchor-avoid-header mt-5 w-full rounded-lg text-center lg:right-0 lg:mt-0 lg:w-2/3 lg:pl-[5.5rem]"
        >
          <FormContact />
        </div>
        <!-- Fin Bloc formulaire -->
      </div>
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import { computed } from 'vue'

import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import FormContact from '@/vuejs/modules/contact/component/FormComponent.vue'
import MailIconComponent from '@/vuejs/modules/shared/icon/MailIconComponent.vue'
import PhoneIconComponent from '@/vuejs/modules/shared/icon/PhoneIconComponent.vue'

import { storeToRefs } from 'pinia'
import { useChannelStore } from '@/vuejs/stores/channel'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

const { channel, formattedPhoneNumber } = storeToRefs(useChannelStore())

const customerService = computed((): string => {
  return (
    channel?.value?.options?.CONTACT_TEXT_UNDER_TITLE ??
    'Notre service adhérents'
  )
})
</script>

<style scoped>
.anchor-avoid-header {
  padding-top: 40px;
  margin-top: -40px;
}
</style>
