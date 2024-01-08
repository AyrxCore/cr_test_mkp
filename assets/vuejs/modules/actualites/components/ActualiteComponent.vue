<template>
  <div
    class="mx-auto my-5 flex h-[524px] flex-col rounded-md bg-white px-6 py-4 md:my-0"
  >
    <div class="mx-auto items-center">
      <RouterLink
        :to="{ name: PageList.NEWS_ITEM, params: { slug: content.slug } }"
      >
        <img
          :src="content.page_actus_img_desktop"
          :alt="`Image ${content.articleTitle}`"
          class="mx-auto h-[205px!important] w-[334px!important]"
        />
      </RouterLink>
    </div>
    <div class="mt-5">
      <p
        class="mb-3 w-max rounded-md px-2 py-1 text-white"
        :style="{ background: content.categoryColor }"
      >
        {{ content.categoryName }}
      </p>
      <span class="text-gray-500">
        {{ formattedDate }}
      </span>
    </div>
    <h3 class="mt-2 text-[25px] font-bold text-primary">
      <RouterLink
        :to="{ name: PageList.NEWS_ITEM, params: { slug: content.slug } }"
        class="truncate-custom truncate-custom-2 text-primary"
      >
        {{ content.articleTitle }}
      </RouterLink>
    </h3>
    <p class="truncate-custom truncate-custom-3 mt-1 text-lg">
      {{ content.articleTeaser }}
    </p>
    <RouterLink
      :to="{ name: PageList.NEWS_ITEM, params: { slug: content.slug } }"
      class="mt-4 text-sm font-medium text-primary underline"
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
  content: {
    type: Object as PropType<ExpertContent>,
    required: true,
  },
})

const formattedDate = computed((): string => {
  return format(new Date(props.content.date), 'dd/MM/yyyy')
})
</script>

<style scoped></style>
