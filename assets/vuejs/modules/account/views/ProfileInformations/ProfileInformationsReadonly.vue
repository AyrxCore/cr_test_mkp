<template>
  <AccountPage>
    <template #right-side>
      <h3 class="primary text-title-35 mb-2 mt-2 md:mt-0">
        Les coordonnées
      </h3>
      <!-- Bloc email -->
      <div class="bloc-contact-information">
        <div class="">
          <h3 class="primary mb-2 text-[20px]">E-mail</h3>
          <p class="mb-2.5">E-mail: {{ user.account.subaccount.email }}</p>

        </div>
        <div class="">
          <div class="float-right w-fit px-2 py-1 text-white">
            <RouterLink :to="{name: PageList.CONTACT_INFORMATION_EMAIL_EDIT}">
              <EditIconComponent />
            </RouterLink>
          </div>
        </div>
      </div>
      <!-- Fin bloc email -->

      <!-- Bloc password -->
      <div class="bloc-contact-information">
        <div class="">
          <h3 class="primary mb-2 text-[20px]">Mot de passe</h3>
          <p class="mb-2.5">Mot de passe:</p>
        </div>
        <div class="">
          <div class="float-right w-fit px-2 py-1 text-white">
            <RouterLink :to="{name: PageList.CONTACT_INFORMATION_PASSWORD_CHANGE}">
              <EditIconComponent />
            </RouterLink>
          </div>
        </div>
      </div>
      <!-- Fin bloc password -->

      <!-- Bloc coordonnées -->
      <div class="bloc-contact-information">
        <div class="">
          <h3 class="primary mb-2 text-[20px]">Coordonnées</h3>
          <p class="mb-2.5">Nom : {{ user.account.subaccount.lastname }}</p>
          <p class="mb-2.5">Prénom : {{ user.account.subaccount.firstname }}</p>
          <p class="mb-2.5">Téléphone fixe : {{ user.account.subaccount.phone }}</p>
        </div>
        <div class="">
          <div class="float-right w-fit px-2 py-1 text-white">
            <RouterLink :to="{name: PageList.CONTACT_INFORMATION_DETAILS_EDIT}">
              <EditIconComponent />
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
import { AccountPageList } from '@/vuejs/modules/account/routerAccount'
import { computed } from 'vue'
import { useUserStore } from '@/vuejs/stores/user'
import { storeToRefs } from 'pinia'
import {PageList} from '@/vuejs/router'

const userStore = useUserStore()
const { user } = storeToRefs(userStore)

const tab = computed(() => {
  return AccountPageList.CONTACT_INFORMATION
})
</script>

<style scoped>
.bloc-contact-information {
  @apply mb-3 flex justify-between rounded-lg bg-white p-3 text-gray-500 md:p-6;
}
</style>
