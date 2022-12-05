<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 sm:px-8">
      <breadcrumb-shared-component :current-page="'Actualités'" />
      <div class="w-[100%] max-w-screen-2xl">
        <ContactUsButtonComponent />
        <h3 class="text-[35px] text-primary">Nos contenus experts</h3>
        <!-- Bloc liste des actus -->
        <div class="m-auto my-2 grid w-[100%] grid-cols-4 gap-4">
          <div class="col-span-3 border">
            <div class="m-auto grid grid-cols-3 gap-4">
              <div v-for="(actualite, key) in listActualites" :key="key">
                <ActualiteComponentComponent :actualite="actualite" />
              </div>
            </div>
          </div>
          <div>
            <h3 class="text-[25px] text-primary">Catégories</h3>
            <p
              v-for="categorie in categories"
              :key="categorie.id"
              class="mb-3 w-max rounded-md px-2 py-1 text-white"
              :class="'bg-[' + categorie.color + ']'"
            >
              {{ categorie.name }}
            </p>
            <div class="mt-10 h-[auto] justify-center rounded-md bg-white p-2">
              <h3 class="mb-5 text-lg font-bold text-primary">
                Vous souhaitez recevoir ces contenus expert directement par
                email ?
              </h3>
              <InputButtonComponent
                placeholder="Votre email"
                :btn-color="'bg-purple-600'"
              >
                S'inscrire
              </InputButtonComponent>
              <p class="mt-4 text-sm text-gray-500">
                Votre adresse email sera uniquement utilisée pour vous envoyer
                nos newsletters. Vous pourrez vous désabonner à tout moment via
                le lien intégré dans la newsletter.
                <a href="#" class="font-bold underline">
                  En savoir plus sur la gestion de vos données et vos droits</a
                >
              </p>
            </div>
          </div>
        </div>
        <!-- Fin bloc liste actu -->

        <!-- Bloc ressources -->
        <div class="mt-20 h-[507px]">
          <div class="m-auto my-2 grid w-[100%] grid-cols-2 gap-4">
            <div>
              <h3
                class="mt-20 w-[205px] bg-gradient-to-r from-secondary via-gradient-1 to-gradient-2 bg-clip-text text-[35px] text-transparent"
              >
                Ressources
              </h3>
              <p class="mt-4 w-[705px] pr-[11.5rem] text-sm text-gray-500">
                Texte expliquant notre bibliothèque de ressources: guides
                thématique, explication de loi etc.
              </p>
              <div class="mt-8">
                <h4 class="inline-flex text-[22px] text-primary">
                  <CheckCircleInIconComponent class="mt-2 mr-2" /> Fiche
                  pratique sur la loi montagne
                </h4>
                <p class="ml-7 pr-[11.5rem] text-sm text-gray-500">
                  Texte. pour donner envie. Ce que vous devez savoir sur la loi
                  montagne et la mise en conformité
                </p>
              </div>
              <div class="mt-4">
                <h4 class="inline-flex text-[22px] text-primary">
                  <CheckCircleInIconComponent class="mt-2 mr-2" /> Fiche
                  pratique sur la loi montagne
                </h4>
                <p class="ml-7 pr-[11.5rem] text-sm text-gray-500">
                  Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                  Curabitur vitae urna nec dolor ultrices cursus at quis magna.
                </p>
              </div>
            </div>
            <div>
              <img
                :src="guideQantisImgFile"
                alt="Picture"
                class="items-center sm:mx-auto"
              />
            </div>
          </div>
        </div>
        <!-- Fin bloc  ressources -->
      </div>
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import { getImage } from '@/vuejs/services/utils'
import defaultImage from '@/vuejs/assets/img/default-image.png'
import guideQantisImg from '@/vuejs/assets/img/samples/guide-qantis.png'
import { computed, ref } from 'vue'
import ActualiteComponentComponent from '@/vuejs/modules/actualites/components/ActualiteComponentComponent.vue'
import InputButtonComponent from '@/vuejs/modules/shared/InputButtonComponent.vue'
import CheckCircleInIconComponent from '@/vuejs/modules/shared/icon/CheckCircleInIconComponent.vue'

const defaultImageFile = getImage(defaultImage)
const guideQantisImgFile = getImage(guideQantisImg)

const categories = ref([
  {
    id: 'partner',
    name: 'Partenaires',
    color: '#050056',
  },
  {
    id: 'rse',
    name: 'RSE',
    color: '#65ac5d',
  },
  {
    id: 'actualites',
    name: 'Actualités',
    color: '#9553FF',
  },
  {
    id: 'evenements',
    name: 'Evénements',
    color: '#00C7FF',
  },
  {
    id: 'bons_plans',
    name: 'Bons plans',
    color: '#404FE6',
  },
])

const listActualites = computed(() => {
  const actualites = []
  let actualite = null
  for (let i = 0; i < 9; i++) {
    const index = Math.floor(Math.random() * categories.value.length)
    actualite = {
      name: "Titre de l'article qui donne envie d'en savoir plus et de cliquer",
      categorie: categories.value[index],
      date: '19/09/2022',
      description: "aperçu du début de l'actualité pour donner envie de lire.",
      img: defaultImageFile,
    }

    actualites.push(actualite)
  }

  return actualites
})
</script>

<style scoped></style>
