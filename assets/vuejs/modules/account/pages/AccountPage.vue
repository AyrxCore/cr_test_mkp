<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-4 sm:px-8">
      <breadcrumb-shared-component :current-page="'Mon compte'" />
      <div class="w-[100%] max-w-screen-2xl">
        <ContactUsButtonComponent />
      </div>
      <RouterLink
        :to="{ name: MainPageList.HOME_PAGE }"
        class="my-4 flex items-center text-[14px] text-secondary lg:my-7"
      >
        <ArrowLeftIconComponent class="mr-2" />
        Retour sur la page d'accueil
      </RouterLink>
      <div class="m-auto max-w-screen-2xl">
        <slot name="header" />
        <div class="mt-10 gap-11 xl:grid xl:grid-cols-4">
          <DropdownListComponent>
            <template #button-label>Mon compte</template>
            <template #content>
              <div class="flex flex-col-reverse gap-4 xl:grid">
                <div class="rounded-lg bg-white pt-2 xl:p-7">
                  <div class="hidden xl:flex xl:flex-col">
                    <h3
                      class="tex-primary text-md mb-2 font-bold xl:text-[20px]"
                    >
                      {{ user.firstName }} test
                      <span class="uppercase">{{ user.lastName }}</span>
                    </h3>
                    <p class="font-bold text-gray-500">Qantis</p>
                    <!-- <p class="mb-4 text-gray-500">Statut:</p> -->

                    <p
                      v-if="user.account.adherent.reducceCode"
                      class="font-bold text-gray-500"
                    >
                      Code Bonuus {{ user.account.adherent.reducceCode }}
                    </p>
                  </div>
                  <div class="sticky bottom-0 flex">
                    <DisconnectIconComponent class="mr-2 xl:hidden" />
                    <a
                      href="#"
                      class="w-fit border-b-2 border-gray-500 text-gray-500 hover:border-b-2 hover:border-purple-600"
                      @click="onLogout"
                    >
                      Déconnexion
                    </a>
                  </div>
                </div>
                <div class="grid gap-4">
                  <div class="rounded-lg bg-white xl:p-7">
                    <h3
                      class="text-md mb-2 font-bold text-primary xl:text-[20px]"
                    >
                      Mes contacts
                    </h3>
                    <div
                      class="items-center text-sm text-gray-500 md:text-base"
                    >
                      <span class="flex">
                        <ChevronRightIconComponent :stroke-color="'#5E6875'" />
                        Service adhérents
                      </span>
                      <a class="ml-6" :href="`tel:${PHONE_ANIMATION}`">
                        {{ PHONE_ANIMATION }}
                      </a>
                      <span class="mt-2 flex">
                        <ChevronRightIconComponent :stroke-color="'#5E6875'" />
                        Contactez-nous
                      </span>
                      <a class="ml-6" :href="`mailto:${MAIL_ANIMATION}`">
                        {{ MAIL_ANIMATION }}
                      </a>
                    </div>
                  </div>
                  <AccountSidebar />
                </div>
              </div>
            </template>
          </DropdownListComponent>

          <div class="col-span-3">
            <slot name="right-side" />
          </div>
        </div>
        <div class="items-center p-6 text-center">
          <p class="text-sm text-gray-500 md:text-base lg:text-lg">
            Les informations liées à votre compte restent strictement
            confidentielles et ne sont utilisées que conformément à notre
            <a href="#" class="font-bold underline decoration-2">
              Politique de confidentialité</a
            >
          </p>
        </div>
      </div>
    </div>
  </BaseTemplate>
</template>

<script lang="ts" setup>
import { useUserStore } from '@/vuejs/stores/user'
import { storeToRefs } from 'pinia'

import AccountSidebar from '@/vuejs/modules/account/components/sidebar/AccountSidebar.vue'
import ArrowLeftIconComponent from '@/vuejs/modules/shared/icon/ArrowLeftIconComponent.vue'
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/ChevronRightIconComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import DisconnectIconComponent from '@/vuejs/modules/shared/icon/DisconnectIconComponent.vue'
import DropdownListComponent from '@/vuejs/modules/shared/DropdownListComponent.vue'

import { MAIL_ANIMATION, PHONE_ANIMATION } from '@/vuejs/services/const'
import { MainPageList } from '@/vuejs/router/pages-list'

const userStore = useUserStore()
const { user } = storeToRefs(userStore)
const onLogout = async (e: Event): Promise<void> => {
  e.preventDefault()
  ;(await userStore.logout()) && location.reload()
}
</script>

<style lang="postcss">
.text-title-35 {
  @apply text-[23px] md:text-[29px] lg:text-[35px];
}
</style>
