<template>
  <AccountPage>
    <template #right-side>
      <h3 class="primary mb-2 page-principal-title mt-2 md:mt-0">Les coordonnées</h3>
      <!-- Bloc email -->
      <div
        class="bloc-contact-information"
      >
        <div class="">
          <h3 class="primary mb-2 text-[20px]">E-mail et identifiants</h3>
          <p class="mb-2.5">E-mail: {{ user.email }}</p>
        </div>
        <div class="">
          <div class="float-right w-fit px-2 py-1 text-white">
            <EditIconComponent />
          </div>
        </div>
      </div>
      <!-- Fin bloc email -->

      <!-- Bloc password -->
      <div
        class="bloc-contact-information"
      >
        <div class="">
          <h3 class="primary mb-2 text-[20px]">Mot de passe</h3>
          <p class="mb-2.5">Mot de passe:</p>
        </div>
        <div class="">
          <div class="float-right w-fit px-2 py-1 text-white">
            <button><EditIconComponent /></button>
          </div>
        </div>
      </div>
      <!-- Fin bloc password -->

      <!-- Bloc coordonnées -->
      <div
        class="bloc-contact-information"
      >
        <div class="">
          <h3 class="primary mb-2 text-[20px]">Coordonnées</h3>
          <p class="mb-2.5">Nom : {{ user.lastname }}</p>
          <p class="mb-2.5">Prénom : {{ user.firstname }}</p>
          <p class="mb-2.5">Téléphone fixe:</p>
          <p class="mb-2.5">Téléphone mobile:</p>
        </div>
        <div class="">
          <div class="float-right w-fit px-2 py-1 text-white">
            <button><EditIconComponent /></button>
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
import { useUserStore } from '@/vuejs/stores/user';
import { storeToRefs } from 'pinia';

const userStore = useUserStore()
const { user } = storeToRefs(userStore)

const tab = computed(() => {
  return AccountPageList.CONTACT_INFORMATION
})
</script>

<style scoped>
  .bloc-contact-information {
    @apply mb-3 flex justify-between rounded-lg bg-white p-3 md:p-6 text-gray-500;
  }
</style>
