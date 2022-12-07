<template>
  <AccountPage>
    <template #header>
      <h3 class="primary mb-2 text-[35px]">
        Bonjour,
        <span class="text-gradient">
          {{ user.firstname }}
          <span class="uppercase">{{ user.lastname }}</span>
        </span>
      </h3>
    </template>
    <template #right-side>
      <h3 class="primary mb-2 text-[35px]">Les dernières commandes</h3>
      <OrderDetailsComponent
        v-for="(order, key) in listOrders"
        :key="key"
        :order="order"
      />
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>
import { ref } from 'vue'
import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import { user } from '@/vuejs/modules/account'
import OrderDetailsComponent from '@/vuejs/modules/account/components/OrderAccountComponent.vue'

const listOrders = ref([
  {
    name: 'Commande N°1',
    buyer: 'Test buyer 1',
    date: '01/01/2023',
    statut: {
      name: 'En cours',
      color: 'bg-qantis',
    },
  },
  {
    name: 'Commande N°2',
    buyer: 'Test buyer 2',
    date: '01/01/2023',
    statut: {
      name: 'Livré',
      color: 'bg-green-500',
    },
  },
  {
    name: 'Commande N°3',
    buyer: 'Test buyer 3',
    date: '01/01/2023',
    statut: {
      name: 'En attente de validation',
      color: 'bg-red-600',
    },
  },
])
</script>

<style scoped></style>
