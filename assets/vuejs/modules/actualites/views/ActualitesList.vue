<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 sm:px-8">
      <breadcrumb-shared-component :current-page="'Actualités'"/>
      <div class="w-[100%] max-w-screen-2xl">
        <ContactUsButtonComponent/>
        <h3 class="text-[35px] text-primary">Nos contenus experts</h3>
        <!-- Bloc liste des actus -->
        <div class="m-auto my-2 grid w-[100%] grid-cols-4 gap-4">
          <div class="col-span-3">
            <div class="m-auto grid grid-cols-3 gap-4">
              <div v-for="contenu in expertsContents"
                   :key="contenu.id"
              >
                <ActualiteComponentComponent :contenu="contenu"/>
              </div>
            </div>
          </div>
          <div>
            <h3 class="text-[25px] text-primary">Catégories</h3>
            <p
                v-for="category in getExpertsContentsCategories"
                :key="category.id"
                class="mb-3 w-max rounded-md px-2 py-1 text-white"
                :class="category.color"
                :style="{'background': category.color}"
            >
              {{ category.name }}
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
                  class="bg-gradient mt-20 w-[205px] bg-clip-text text-[35px] text-transparent"
              >
                Ressources
              </h3>
              <p class="mt-4 w-[705px] pr-[11.5rem] text-sm text-gray-500">
                Texte expliquant notre bibliothèque de ressources: guides
                thématique, explication de loi etc.
              </p>
              <div class="mt-8">
                <h4 class="inline-flex text-[22px] text-primary">
                  <CheckCircleInIconComponent class="mt-2 mr-2"/>
                  Fiche
                  pratique sur la loi montagne
                </h4>
                <p class="ml-7 pr-[11.5rem] text-sm text-gray-500">
                  Texte. pour donner envie. Ce que vous devez savoir sur la loi
                  montagne et la mise en conformité
                </p>
              </div>
              <div class="mt-4">
                <h4 class="inline-flex text-[22px] text-primary">
                  <CheckCircleInIconComponent class="mt-2 mr-2"/>
                  Fiche
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
import {getImage} from '@/vuejs/services/utils'
import guideQantisImg from '@/vuejs/assets/img/samples/guide-qantis.png'
import {onMounted, ref} from 'vue'
import ActualiteComponentComponent from '@/vuejs/modules/actualites/components/ActualiteComponent.vue'
import InputButtonComponent from '@/vuejs/modules/shared/InputButtonComponent.vue'
import CheckCircleInIconComponent from '@/vuejs/modules/shared/icon/CheckCircleInIconComponent.vue'
import {useExpertContentStore} from '@/vuejs/stores/expertContent'
import {storeToRefs} from 'pinia'
import {ExpertContent} from '@/vuejs/types/ExpertContent'

const expertContentStore = useExpertContentStore()
const { getExpertsContentsCategories} = storeToRefs(expertContentStore)
const expertsContents = ref<Array<ExpertContent>>([])

const guideQantisImgFile = getImage(guideQantisImg)

onMounted(async () => {
  expertsContents.value = await expertContentStore.init()
})
</script>

<style scoped></style>
