<template>
  <BaseTemplate>
    <div class="xs:w-full m-auto mt-4 mb-16 flex-1 px-4 sm:px-8">
      <BreadcrumbSharedComponent
        current-page="Mon compte"
        gtm-event-name="click_account_details_breadcrumb"
      />
      <div class="sm:pr-12">
        <slot name="header" />
        <div class="mt-7 gap-11 xl:grid xl:grid-cols-4">
          <DropdownListComponent>
            <template #button-label>Mon compte</template>
            <template #content>
              <div class="flex flex-col-reverse gap-4 p-4 xl:grid xl:p-0">
                <div class="rounded-lg bg-white pt-2 xl:p-7">
                  <div class="hidden xl:flex xl:flex-col">
                    <h3 class="text-md mb-2 font-bold xl:text-2xl">
                      {{ user.firstName }}
                      <span class="uppercase">{{ user.lastName }}</span>
                    </h3>
                    <p class="mb-2 text-lg">
                      {{ user.externalApiData.buyer.name }}
                    </p>
                  </div>
                  <div class="sticky bottom-0 flex">
                    <DisconnectIconComponent
                      class="mr-2"
                      :stroke="channelPrimaryColor"
                    />
                    <a
                      href="#"
                      class="w-fit border-b-2 border-primary hover:border-secondary"
                      @click="onLogout"
                    >
                      Déconnexion
                    </a>
                  </div>
                </div>
                <div class="grid gap-4">
                  <div
                    v-if="user.account.adherent.reducceCode"
                    class="rounded-lg bg-secondary p-2 xl:p-7"
                    :class="'text-' + betterTextColor('secondary')"
                  >
                    Code Avantages Salariés :
                    {{ user.account.adherent.reducceCode }}
                  </div>
                  <AccountSidebar />
                  <div class="rounded-lg bg-white xl:p-7">
                    <h3 class="mb-2 text-lg font-bold xl:text-2xl">
                      Mes contacts
                    </h3>
                    <div class="items-center text-sm md:text-base xl:text-lg">
                      <span>
                        Le service adhérents sera ravi de répondre à vos
                        questions du lundi au vendredi de 8h30 à 18h
                      </span>
                      <div class="mt-4 text-primary underline">
                        <a
                          :href="`tel:${channel.phoneNumber}`"
                          class="hover:font-bold"
                          @click="sendGaEvent('click_account_phone')"
                        >
                          {{ channelPhoneNumber }}
                        </a>
                      </div>
                      <div class="mt-2 text-primary underline">
                        <a
                          :href="`mailto:${channel.email}`"
                          class="hover:font-bold"
                          @click="sendGaEvent('click_account_email')"
                        >
                          {{ channel.email }}
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </DropdownListComponent>
          <div class="col-span-3">
            <slot name="right-side" />
          </div>
        </div>
      </div>
    </div>
  </BaseTemplate>
</template>

<script lang="ts" setup>
import { useUserStore } from '@/vuejs/stores/user'
import { useChannelStore } from '@/vuejs/stores/channel'
import { storeToRefs } from 'pinia'

import AccountSidebar from '@/vuejs/modules/account/components/sidebar/AccountSidebar.vue'
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import DisconnectIconComponent from '@/vuejs/modules/shared/icon/DisconnectIconComponent.vue'
import DropdownListComponent from '@/vuejs/modules/shared/DropdownListComponent.vue'
import { betterTextColor } from '@/vuejs/services/utils'
import { OPTIONAL_FRONT_BLOCKS } from '@/vuejs/services/const'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

const userStore = useUserStore()
const { user } = storeToRefs(userStore)

const {
  channel,
  formattedPhoneNumber: channelPhoneNumber,
  channelPrimaryColor,
} = storeToRefs(useChannelStore())

const onLogout = async (e: Event): Promise<void> => {
  e.preventDefault()
  ;(await userStore.logout()) && location.reload()
  sendGaEvent('click_account_logout')
}
</script>

<style lang="postcss">
.text-title-34 {
  @apply text-[30px] lg:text-[34px];
}
</style>
