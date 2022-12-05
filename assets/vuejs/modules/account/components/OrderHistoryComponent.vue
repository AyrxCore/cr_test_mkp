<template>
  <div class="text-[15px] text-gray-500 rounded-lg bg-white ">
    <div class="flex px-1.5 py-2.5 mb-2.5 w-full">
      <div class="w-3/12">
        <span class="text-gray-500 flex">{{ order.date }}</span>
      </div>
      <div class="w-4/12">
        <span class="text-gray-500 flex">Commande :  {{ order.name }}</span>
        <span class="text-gray-500 flex">Référence : {{ order.reference }}</span>
        <span class="text-gray-500 flex">Articles : {{ order.numberArticle }}</span>
        <span class="text-gray-500 flex">Acheteur : <span class="uppercase ml-1"> {{ order.sellerName }}</span></span>
        <span class="text-gray-500 flex">Compte : <span class="uppercase ml-1"> {{ order.accountName }}</span></span>
      </div>
      <div class="w-2/12">
        <div
          class="text-[14px] text-white px-1 py-1 w-fit rounded-md mb-2"
          :class="order.statut.color"
          :title="order.statut.name"
        >
          {{order.statut.name}}
        </div>
      </div>
      <div class="w-3/12">
        <span class="text-gray-500 flex">{{ order.address.name }}</span>
        <span class="text-gray-500 flex">{{ order.address.street }}</span>
        <span class="text-gray-500 flex">{{ order.address.postalCode }} {{ order.address.city }}</span>
        <span class="text-gray-500 flex">{{ order.address.country }}</span>
      </div>
      <div class="w-3/12">
        <span class="primary flex font-bold">{{ order.total }}€ HT</span>
        <span class="text-gray-500 flex">({{ order.total }}€ HT)</span>
      </div>
      <div class="w-1/12">
        <a  href="#" class="underline">Détails</a>
      </div>
    </div>
    <div v-if="order.statut.type" class="flex px-1.5 py-2.5 mb-2.5 w-full justify-end">
      <DefaultButtonComponent
        :btn-text-color="'text-purple-500'"
        :btn-color="'bg-transparent'"
        :rounded="'rounded-full'"
        class="border border-purple-600"
      >
        <ArrowRightIconComponent
          class="mr-2 w-4"
        />
        Rejeter
      </DefaultButtonComponent>
      <GradientButtonComponent
        type="submit"
        class="justify-center text-center py-1.5"
      >
        <ArrowRightIconComponent
          :stroke-color="'#FFFFFF'"
          class="mr-2 w-4"
        />
        Appliquer
      </GradientButtonComponent>
    </div>
  </div>
</template>
<script lang="ts" setup>
import DefaultButtonComponent from '@/vuejs/modules/shared/DefaultButtonComponent.vue'
import GradientButtonComponent from '@/vuejs/modules/shared/GradientButtonComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
const props = defineProps({
  order: {
    required: true,
    type: Object,
  },
})

</script>
