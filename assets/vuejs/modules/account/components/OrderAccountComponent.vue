<template>
  <div class="py-6 px-6 justify-between bg-white rounded-lg flex text-gray-500 mb-3">
    <div class="">
      <p class="mb-2.5"> Nom de la commande: {{order.name}}</p>
      <p class="mb-2.5"> Commande passé par: {{order.buyer}}</p>
      <p class=""> Date: {{order.date}}</p>
    </div>
    <div class="">
      <div
        class="float-right text-white px-2 py-1 w-fit rounded-md"
        :class="order.statut.color"
      >
        {{order.statut.name}}
      </div>
      <div class=" mt-14">
        <a href="#" class="underline decoration-2 underline-offset-4 hover:decoration-purple-600 inline-flex items-center">
          Voir le détail de la commade
          <ArrowRightIconComponent :stroke-color="'#5E6875'" class="ml-2"/>
        </a>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
const props = defineProps({
  order: {
    required: true,
    type: Array,
  },
})

</script>
