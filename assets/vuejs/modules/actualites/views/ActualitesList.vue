<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="m-auto my-4 max-w-screen-2xl flex-1 sm:px-8 xs:w-[100%] text-cotext">
      <breadcrumb-shared-component :current-page="'Actualités'" />
      <div class="w-[100%] max-w-screen-2xl">
        <ContactUsButtonComponent />
        <h3 class=" primary text-[35px]">Nos contenus experts</h3>
        <!-- Bloc liste des actus -->
        <div class="m-auto w-[100%] my-2 grid grid-cols-4 gap-4">
          <div class="col-span-3 border">
            <div class="m-auto grid grid-cols-3 gap-4">
              <div v-for="(actualite, key) in listActualites" :key="key">
                <ActualiteComponentComponent :actualite="actualite" />
              </div>
            </div>
          </div>
          <div>
            <h3 class=" primary text-[25px]">Catégories</h3>
            <p
              v-for="categorie in categories" :key="categorie.id"
              class="px-2 py-1 rounded-md w-max mb-3 text-white"
              :class="'bg-[' + categorie.color +']'"
            >
              {{categorie.name}}
            </p>
            <div class=" mt-10 justify-center bg-white p-2 rounded-md h-[auto]">
              <h3 class="primary text-lg mb-5 font-bold">
                Vous souhaitez recevoir ces contenus expert directement par email ?
              </h3>
              <input-button-component
                placeholder="Votre email"
                :btn-color="'bg-purple-600'">
                S'inscrire
              </input-button-component>
              <p class="text-[14px] text-gray-500 mt-4">
                Votre adresse email sera uniquement utilisée pour vous envoyer nos newsletters.
                Vous pourrez vous désabonner à tout moment via le lien intégré dans la newsletter.
                <a href="#" class="font-bold underline"> En savoir plus sur la gestion de vos données et vos droits</a>
              </p>
            </div>
          </div>
        </div>
        <!-- Fin bloc liste actu -->

        <!-- Bloc ressources -->
        <div class="h-[507px] mt-20">
          <div class="m-auto w-[100%] my-2 grid grid-cols-2 gap-4">
            <div>
              <h3 class="text-[35px] text-transparent bg-clip-text bg-gradient-to-r from-purple-500 via-blue-500 to-cyan-500 w-[205px] mt-20">
                Ressources
              </h3>
              <p class="text-[14px] text-gray-500 mt-4 w-[705px] pr-[11.5rem]">
                Texte expliquant notre bibliothèque de ressources: guides thématique, explication de loi etc.
              </p>
              <div class="mt-8">
                <h4 class="primary text-[22px] inline-flex"> <CheckCircleInIconComponent  class="mt-2 mr-2"/> Fiche pratique sur la loi montagne</h4>
                <p class="text-[14px] text-gray-500 ml-7 pr-[11.5rem]">
                  Texte. pour donner envie. Ce que vous devez savoir sur la loi montagne et la mise en conformité
                </p>
              </div>
              <div class="mt-4">
                <h4 class="primary text-[22px] inline-flex"> <CheckCircleInIconComponent  class="mt-2 mr-2"/> Fiche pratique sur la loi montagne</h4>
                <p class="text-[14px] text-gray-500 ml-7 pr-[11.5rem]">
                  Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur vitae urna nec dolor ultrices cursus at quis magna.
                </p>
              </div>
            </div>
            <div>
              <img :src="guideQantisImgFile" alt="Picture" class="sm:mx-auto items-center "/>
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
  }
])

const listActualites = computed(() => {
  const actualites = []
  let actualite = null
  for (let i = 0; i < 9; i++) {
    const index = Math.floor(Math.random() * categories.value.length)
    actualite = {
      name: 'Titre de l\'article qui donne envie d\'en savoir plus et de cliquer',
      categorie: categories.value[index],
      date: '19/09/2022',
      description: 'aperçu du début de l\'actualité pour donner envie de lire.',
      img: defaultImageFile,
    }

    actualites.push(actualite)
  }

  return actualites
})

</script>

<style scoped></style>
