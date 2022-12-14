<template>
  <AccountPage>
    <template #right-side>
      <h3 class="primary mb-2 text-[35px]">Les coordonnées</h3>
      <!-- Bloc email -->
      <div class="py-6 px-6 justify-between bg-white rounded-lg flex text-gray-500 mb-3">
        <div class="">
          <h3 class="primary mb-2 text-[20px]">E-mail et identifiants</h3>
          <p class="mb-2.5"> E-mail: {{user.email}}</p>
        </div>
        <div class="">
          <div class="float-right text-white px-2 py-1 w-fit">
            <EditIconComponent />
          </div>
        </div>
      </div>
      <!-- Fin bloc email -->

      <!-- Bloc password -->
      <div class="py-6 px-6 justify-between bg-white rounded-lg flex text-gray-500 mb-3">
        <div class="">
          <h3 class="primary mb-2 text-[20px]">Mot de passe</h3>
          <p class="mb-2.5"> Mot de passe: </p>
        </div>
        <div class="">
          <div class="float-right text-white px-2 py-1 w-fit">
            <button><EditIconComponent /></button>
          </div>
        </div>
      </div>
      <!-- Fin bloc password -->

      <!-- Bloc coordonnées -->
      <div class="py-6 px-6 justify-between bg-white rounded-lg flex text-gray-500 mb-3">
        <div class="">
          <h3 class="primary mb-2 text-[20px]">Coordonnées</h3>
          <p class="mb-2.5"> Nom : {{user.lastname}}</p>
          <p class="mb-2.5"> Prénom : {{user.firstname}}</p>
          <p class="mb-2.5"> Téléphone fixe: </p>
          <p class="mb-2.5"> Téléphone mobile: </p>
        </div>
        <div class="">
          <div class="float-right text-white px-2 py-1 w-fit">
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

const tab = computed(() => {
  return AccountPageList.CONTACT_INFORMATION
})

</script>

<style scoped></style>
