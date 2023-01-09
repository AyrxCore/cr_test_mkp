<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-5 sm:px-8">
      <breadcrumb-shared-component
        :list-url="listUrl"
        :current-page="'Actualité'"
      />
      <div class="w-[100%] max-w-screen-2xl">
        <ContactUsButtonComponent />
      </div>
      <div
        class="m-auto my-2 flex w-[100%] max-w-screen-2xl flex-col lg:grid lg:grid-cols-2 lg:gap-4"
      >
        <!-- Bloc text actualité -->
        <div>
          <h3 class="primary text-title-35 mb-2">
            {{ contenusExpert[1].title }}
          </h3>
          <span
            class="mr-2 w-max rounded-md px-2 py-1 text-white text-white"
            :class="contenusExpert[1].categorie.color"
            >{{ contenusExpert[1].categorie.name }}</span
          >
          <span class="text-gray-500">{{ contenusExpert[1].date }}</span>
          <div class="mt-5 h-[auto] rounded-lg">
            <p class="whitespace-pre-line text-gray-500">
              {{ article.description }}
            </p>
          </div>
          <a
            href="#"
            class="default-button-gradient mt-4 inline-flex justify-center px-3.5 py-3 text-center font-bold text-white"
          >
            <ArrowRigntIconComponent
              class="mt-1 mr-2 w-4 items-center"
              :stroke-color="'#FFFFFF'"
            />
            Découvrir ce partenaire
          </a>
        </div>
        <!-- Fin Bloc text actualité -->

        <!-- Bloc image -->
        <div class="mt-[7rem] h-[421px] rounded-lg bg-white">
          <img
            :src="contenusExpert[1].img_landscape"
            alt="Picture"
            class="m-auto h-[inherit] items-center"
          />
        </div>
        <!-- Fin Bloc image -->
      </div>

      <!-- Bloc articles recommandés -->
      <div class="mt-10 justify-center">
        <h3 class="primary home-subtitle mb-5">Articles recommandés</h3>
        <ContenusExpertComponent :contenus="contenusExpert" />
      </div>
      <!-- Fin Bloc articles recommandés -->
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import { getImage } from '@/vuejs/services/utils'
import defaultImage from '@/vuejs/assets/img/default-image.png'
import { computed, ref } from 'vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ArrowRigntIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import { contenusExpert } from '@/vuejs/modules/actualites'
import ContenusExpertComponent from '@/vuejs/modules/home/component/ContenusExpertComponent.vue'

const defaultImageFile = getImage(defaultImage)

const articlesRecommandes = computed(() => {
  const articles = []

  for (let i = 0; i < 3; i++) {
    articles.push({
      name: 'Actualité veille\n' + 'Décret tertiaire',
      description: "aperçu du début de l'actualité pour donner envie de lire.",
      img: defaultImageFile,
    })
  }

  return articles
})

const listUrl = ref([
  {
    name: 'Actualités',
    url: '/app/actualites',
  },
])

const article = ref({
  name: "Titre de l'article",
  category: {
    name: 'Partenaires',
    color: 'bg-primary',
  },
  date: '12/12/2022',
  description:
    'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac sem at enim convallis consectetur quis sed diam. Curabitur consequat sagittis tempus. Nulla mollis felis erat, non tincidunt ligula mattis vulputate. Aenean cursus dictum tempor. Proin sit amet quam in diam tempor cursus. Curabitur aliquet ut odio at vehicula. Donec tristique gravida tristique. Sed ullamcorper interdum vestibulum. Proin eu tincidunt justo.\n' +
    '\n' +
    'Curabitur turpis lectus, suscipit et velit non, ornare facilisis justo. In maximus tempor est, sodales congue dui accumsan ut. In bibendum mi nunc, ac aliquet eros placerat eu. Nunc dictum ipsum sed cursus laoreet. Vestibulum tincidunt sapien dolor, sit amet tempus purus posuere quis. Praesent tempus risus ligula, eget rhoncus velit tempus id. Fusce placerat, odio non auctor lacinia, mi libero varius diam, id sagittis ipsum tellus ac erat. Maecenas quis erat maximus, pharetra metus eget, egestas leo. Aliquam eu tortor blandit, dignissim nibh in, elementum elit.',
})
</script>

<style scoped></style>
