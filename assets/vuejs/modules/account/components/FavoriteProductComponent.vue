<template>
  <div class="items-center flex text-lg text-gray-500 rounded-lg items-center bg-white p-2.5 mb-2.5">
    <div class="w-5/12">
      <a href="/app/account/favoris-details" class="underline text-purple-600">{{favorite.name}}</a>
    </div>
    <div class="w-2/12">
      {{favorite.dateCreate}}
    </div>
    <div class="w-2/12">
      {{favorite.dateUpdate}}
    </div>
    <div class="w-2/12">
      {{favorite.numberArticle}} {{favorite.numberArticle > 1 ? 'articles' : 'article'}}
    </div>
    <div class="w-1/12 flex">
        <button><EditIconComponent class="mr-2" /></button>
      <button><TrashIconComponent :stroke-color="'#9866ff'"/></button>
    </div>
  </div>
</template>
<script lang="ts" setup>
import EditIconComponent from '@/vuejs/modules/shared/icon/EditIconComponent.vue'
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'
const props = defineProps({
  favorite: {
    required: true,
    type: Object,
  },
})

</script>
