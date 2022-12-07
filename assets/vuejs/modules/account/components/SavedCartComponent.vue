<template>
  <div class="flex text-lg text-gray-500 rounded-lg bg-white p-2.5 mb-2.5">
    <div class="w-3/12">
      <a href="/app/account/favoris-details" class="underline text-purple-600">{{cart.name}}</a>
    </div>
    <div class="w-2/12">
      {{cart.dateCreate}}
    </div>
    <div class="w-3/12">
      {{cart.numberArticle}} {{cart.numberArticle > 1 ? 'articles' : 'article'}}
    </div>
    <div class="w-2/12">
      <span class="primary flex font-bold">{{ cart.total }}€ HT</span>
      <span class="text-gray-500 flex">({{ cart.total }}€ HT)</span>
    </div>
    <div class="w-2/12 flex">
      <a  href="#" class="underline mr-3">Détails</a>
        <button class="flex"><EditIconComponent class="mr-2" /></button>
        <button class="flex"><TrashIconComponent :stroke-color="'#9866ff'"/></button>
    </div>
  </div>
</template>
<script lang="ts" setup>
import EditIconComponent from '@/vuejs/modules/shared/icon/EditIconComponent.vue'
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'
const props = defineProps({
  cart: {
    required: true,
    type: Object,
  },
})

</script>
