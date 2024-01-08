<template>
  <AccountPage>
    <template #right-side>
      <h3 class="text-title-primary mb-4 mt-2 md:mt-0">Mes coordonnées</h3>
      <!-- Bloc email -->
      <div class="bloc-contact-information">
        <div>
          <h3 class="mb-2 text-2xl font-bold">E-mail</h3>
          <p class="mb-2.5 text-lg">E-mail&nbsp;: {{ user.username }}</p>
          <div
            v-if="emailInformation && !emailInformation.isIso"
            class="rounded bg-orange-400 py-2 px-5 text-sm text-white"
          >
            Demande de changement d'email {{ emailInformation.value }}, en
            attente de validation
          </div>
        </div>
        <div>
          <div class="float-right w-fit px-2 py-1 text-white">
            <RouterLink :to="{ name: PageList.CONTACT_INFORMATION_EMAIL_EDIT }">
              <EditIconComponent :stroke="channelPrimaryColor" />
            </RouterLink>
          </div>
        </div>
      </div>
      <!-- Fin bloc email -->

      <!-- Bloc password -->
      <div class="bloc-contact-information">
        <div>
          <h3 class="mb-2 text-2xl font-bold">Mot de passe</h3>
          <p class="mb-2.5 text-lg">Mot de passe&nbsp;: ********</p>
        </div>
        <div>
          <div class="float-right w-fit px-2 py-1 text-white">
            <RouterLink
              :to="{ name: PageList.CONTACT_INFORMATION_PASSWORD_CHANGE }"
            >
              <EditIconComponent
                :stroke="channelPrimaryColor"
                @click="sendGaEvent('click_account_edit_password')"
              />
            </RouterLink>
          </div>
        </div>
      </div>
      <!-- Fin bloc password -->

      <!-- Bloc coordonnées -->
      <div class="bloc-contact-information">
        <div>
          <h3 class="mb-2 text-2xl font-bold">Coordonnées</h3>
          <p class="mb-2.5 text-lg">Nom&nbsp;: {{ user.lastName }}</p>
          <p class="mb-2.5 text-lg">Prénom&nbsp;: {{ user.firstName }}</p>
          <p class="mb-2.5 text-lg">
            Téléphone fixe&nbsp;: {{ user.account.phone }}
          </p>
        </div>
        <div>
          <div class="float-right w-fit px-2 py-1 text-white">
            <RouterLink
              :to="{ name: PageList.CONTACT_INFORMATION_DETAILS_EDIT }"
            >
              <EditIconComponent
                :stroke="channelPrimaryColor"
                @click="sendGaEvent('click_account_edit_info')"
              />
            </RouterLink>
          </div>
        </div>
      </div>
      <!-- Fin bloc coordonnées -->
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>
import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import EditIconComponent from '@/vuejs/modules/shared/icon/EditIconComponent.vue'
import { useUserStore } from '@/vuejs/stores/user'
import { storeToRefs } from 'pinia'
import { PageList } from '@/vuejs/router'
import { computed } from 'vue'
import { useChannelStore } from '@/vuejs/stores/channel'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

const userStore = useUserStore()
const { user } = storeToRefs(userStore)
const emailInformation = computed(() => {
  const information = user.value.userInfoUpdateRequests.filter(function (el) {
    return el.attribute === 'email'
  })

  return information[0] ?? null
})

const { channelPrimaryColor } = storeToRefs(useChannelStore())
</script>

<style scoped>
.bloc-contact-information {
  @apply mb-3 flex justify-between rounded-lg bg-white p-3 md:p-6;
}
</style>
