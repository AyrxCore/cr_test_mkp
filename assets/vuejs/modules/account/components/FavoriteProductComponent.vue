<template>
  <div
    class="mb-2.5 flex items-center items-center rounded-lg bg-white p-2.5 text-lg text-gray-500"
  >
    <div class="w-5/12">
      <a
        href="/app/account/favoris-details"
        class="text-purple-600 underline"
        >{{ favorite.name }}</a
      >
    </div>
    <div class="w-2/12">
      {{ favorite.dateCreate }}
    </div>
    <div class="w-2/12">
      {{ favorite.dateUpdate }}
    </div>
    <div class="w-2/12">
      {{ favorite.numberArticle }}
      {{ favorite.numberArticle > 1 ? 'articles' : 'article' }}
    </div>
    <div class="flex w-1/12">
      <button><EditIconComponent class="mr-2" /></button>
      <button><TrashIconComponent :stroke-color="'#9866ff'" /></button>
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
