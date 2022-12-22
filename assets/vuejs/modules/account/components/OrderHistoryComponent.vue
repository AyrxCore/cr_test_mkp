<template>
  <div class="flex flex-col rounded-lg bg-white text-sm lg:text-[15px] text-gray-500 mb-5">
    <div class="mb-2.5 flex flex-col w-full px-5 py-2.5 md:px-2.5 md:flex-row">
      <div class="w-full justify-end flex md:w-2/12 lg:w-3/12 md:justify-start">
        <span class="flex text-gray-500">{{ order.date }}</span>
      </div>
      <div class="w-full mt-5 md:w-5/12  md:mt-0 lg:w-4/12">
        <span class="flex text-gray-500">Commande : {{ order.name }}</span>
        <span
          class="flex text-gray-500"
          >Référence : {{ order.reference }}
        </span>
        <span
          class="flex text-gray-500"
          >
          Articles : {{ order.numberArticle }}
        </span>
        <span class="flex text-gray-500"
          >Acheteur :
          <span class="ml-1 uppercase"> {{ order.sellerName }}</span></span
        >
        <span class="flex text-gray-500"
          >Compte :
          <span class="ml-1 uppercase"> {{ order.accountName }}</span></span
        >
      </div>
      <div class="w-fit absolute md:relative flex justify-end md:block md:w-2/12">
        <div
          class="mb-2 w-fit rounded-md px-1 py-1 text-[14px] text-white"
          :class="order.statut.color"
          :title="order.statut.name"
        >
          {{ order.statut.name }}
        </div>
      </div>
      <div class="w-full md:w-3/12">
        <div class="flex md:flex-col">
          <span class="flex text-gray-500">{{ order.address.name }},</span>
          <span class="flex text-gray-500">{{ order.address.street }}</span>
        </div>
        <div class="flex md:flex-col">
          <span
            class="flex text-gray-500"
            >{{ order.address.postalCode }} {{ order.address.city }},
          </span>
          <span class="flex text-gray-500">{{ order.address.country }}</span>
        </div>
      </div>
      <div class="w-full flex my-2 md:my-0 md:flex-col justify-start md:justify-none md:w-3/12">
        <span class="text-primary flex font-bold mr-2 md:mr-0">{{ order.total }}€ HT</span>
        <span class="flex text-gray-500">({{ order.total }}€ HT)</span>
      </div>
      <div class=" md:w-1/12">
        <a href="#" class="underline">Détails</a>
      </div>
    </div>
    <div
      v-if="order.statut.type"
      class="mb-2.5 flex justify-between md:justify-end px-1.5 py-2.5"
    >
      <ButtonComponent
        class="flex button-secondary !bg-transparent !text-secondary text-base !px-2 md:!px-8 border border-secondary lg:text-lg w-1/2 md:w-auto mr-1"
      >
        <ArrowRightIconComponent class="w-4" /> Rejeter
      </ButtonComponent>
      <ButtonComponent class="button-gradient" :is-loading="isLoading">
        <ArrowRightIconComponent class="w-4" /> Appliquer
      </ButtonComponent>
    </div>
  </div>
</template>
<script lang="ts" setup>
import DefaultButtonComponent from '@/vuejs/modules/shared/DefaultButtonComponent.vue'
import GradientButtonComponent from '@/vuejs/modules/shared/GradientButtonComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue';
const props = defineProps({
  order: {
    required: true,
    type: Object,
  },
})
</script>
