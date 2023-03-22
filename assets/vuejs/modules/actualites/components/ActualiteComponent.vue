<template>
  <div class="mx-auto flex h-[524px] flex-col rounded-md bg-white px-6 py-4">
    <div class="mx-auto items-center">
      <RouterLink
        :to="{ name: PageList.ACTUALITE, params: { slug: contenu.slug } }"
      >
        <img
          :src="contenu.page_actus_img_desktop"
          :alt="`Image ${contenu.articleTitle}`"
          class="mx-auto h-[205px!important] w-[334px!important]"
        />
      </RouterLink>
    </div>
    <div class="mt-5">
      <p
        class="mb-3 w-max rounded-md px-2 py-1 text-white"
        :style="{ background: contenu.categoryColor }"
      >
        {{ contenu.categoryName }}
      </p>
      <span class="text-gray-500">
        {{ formattedDate }}
      </span>
    </div>
    <h3 class="mt-2 text-[25px] font-bold text-primary">
      <RouterLink
        :to="{ name: PageList.ACTUALITE, params: { slug: contenu.slug } }"
        class="truncate-custom truncate-custom-2 text-primary"
      >
        {{ contenu.articleTitle }}
      </RouterLink>
    </h3>
    <p class="truncate-custom truncate-custom-3 mt-1 text-lg text-gray-400">
      {{ contenu.articleTeaser }}
    </p>
    <RouterLink
      :to="{ name: PageList.ACTUALITE, params: { slug: contenu.slug } }"
      class="text-sm font-medium text-primary underline"
    >
      Lire l'article
    </RouterLink>
  </div>
</template>

<script lang="ts" setup>
import { format } from 'date-fns'
import { computed, PropType } from 'vue'

import { PageList } from '@/vuejs/router'
import { ExpertContent } from '@/vuejs/types/ExpertContent'

const props = defineProps({
  contenu: {
    type: Object as PropType<ExpertContent>,
    required: true,
  },
})

const formattedDate = computed((): string => {
  return format(new Date(props.contenu.date), 'dd/MM/yyyy')
})
</script>

<style scoped></style>
