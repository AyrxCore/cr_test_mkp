<template>
  <div
      class="mx-auto flex h-[524px] flex-col items-center rounded-md bg-white px-6 py-2"
  >
    <div class="mx-auto items-center">
      <RouterLink
        :to="{ name: PageList.ACTUALITE, params: {slug: contenu.slug} }"
      >
        <img
            :src="contenu.page_actus_img_desktop"
            alt="Image produit"
            class="mx-auto h-[205px!important] w-[334px!important]"
        />
      </RouterLink>
    </div>
    <div class="mt-5 w-full justify-start">
      <p
          class="mb-3 w-max rounded-md px-2 py-1 text-white"
          :style="{'background': contenu.categoryColor}"
      >
        {{ contenu.categoryName }}
      </p>
      <span class="text-gray-500">
        {{ moment(String(contenu.date)).format('DD/MM/YYYY') }}

      </span>
    </div>
    <h3 class="mt-6 text-left text-[25px] font-bold text-primary ">
      <RouterLink
        :to="{ name: PageList.ACTUALITE, params: {slug: contenu.slug} }"
        class="bottom-0 items-center text-primary truncate-custom truncate-custom-2"
      >
        {{ contenu.articleTitle }}
      </RouterLink>
    </h3>
    <p class="mt-1 text-left text-lg text-gray-400 truncate-custom truncate-custom-3">
      {{ contenu.articleTeaser }}
    </p>
    <div class="bottom-0 flex w-full items-end">
      <RouterLink
        :to="{ name: PageList.ACTUALITE, params: {slug: contenu.slug} }"
        class="bottom-0 items-center text-sm font-medium text-primary underline"
      >
        Lire l'article
      </RouterLink>
    </div>
  </div>
</template>
<script lang="ts" setup>
import {useRouter} from 'vue-router'
import {PageList} from '@/vuejs/router'
import {ExpertContent} from '@/vuejs/types/ExpertContent'
import {PropType} from 'vue'
import moment from 'moment'

const router = useRouter()

const props = defineProps({
  contenu: {
    type: Object as PropType<ExpertContent>,
    required: true,
  },
})

const goToArticle = (slug) => {
  router.push({name: PageList.ACTUALITE, params: {slug: slug}})
}

</script>

<style scoped></style>
