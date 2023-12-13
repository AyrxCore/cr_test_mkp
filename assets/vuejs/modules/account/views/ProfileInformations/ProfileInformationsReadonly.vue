<template>
  <AccountPage>
    <template #right-side>
      <h3 class="mb-2 mt-2 text-title-35 text-primary md:mt-0">
        Les coordonnées
      </h3>
      <!-- Bloc email -->
      <div class="bloc-contact-information">
        <div>
          <h3 class="mb-2 text-[20px] text-primary">E-mail</h3>
          <p class="mb-2.5">E-mail: {{ user.username }}</p>
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
              <EditIconComponent :icon-color="channelSecondaryColor" />
            </RouterLink>
          </div>
        </div>
      </div>
      <!-- Fin bloc email -->

      <!-- Bloc password -->
      <div class="bloc-contact-information">
        <div>
          <h3 class="mb-2 text-[20px] text-primary">Mot de passe</h3>
          <p class="mb-2.5">Mot de passe:</p>
        </div>
        <div>
          <div class="float-right w-fit px-2 py-1 text-white">
            <RouterLink
              :to="{ name: PageList.CONTACT_INFORMATION_PASSWORD_CHANGE }"
            >
              <EditIconComponent :icon-color="channelSecondaryColor" />
            </RouterLink>
          </div>
        </div>
      </div>
      <!-- Fin bloc password -->

      <!-- Bloc coordonnées -->
      <div class="bloc-contact-information">
        <div>
          <h3 class="mb-2 text-[20px] text-primary">Coordonnées</h3>
          <p class="mb-2.5">Nom : {{ user.lastName }}</p>
          <p class="mb-2.5">Prénom : {{ user.firstName }}</p>
          <p class="mb-2.5">Téléphone fixe : {{ user.account.phone }}</p>
        </div>
        <div>
          <div class="float-right w-fit px-2 py-1 text-white">
            <RouterLink
              :to="{ name: PageList.CONTACT_INFORMATION_DETAILS_EDIT }"
            >
              <EditIconComponent :icon-color="channelSecondaryColor" />
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

const userStore = useUserStore()
const { user } = storeToRefs(userStore)
const emailInformation = computed(() => {
  const information = user.value.userInfoUpdateRequests.filter(function (el) {
    return el.attribute === 'email'
  })

  return information[0] ?? null
})

const { channelSecondaryColor } = storeToRefs(useChannelStore())
</script>

<style scoped>
.bloc-contact-information {
  @apply mb-3 flex justify-between rounded-lg bg-white p-3 text-gray-500 md:p-6;
}
</style>
