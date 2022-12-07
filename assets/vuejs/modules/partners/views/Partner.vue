<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl">
      <HeaderPartnerComponent />

      <div class="mt-10 mt-5 flex grid grid-cols-9 gap-4 text-gray-600">
        <div class="col-span-5 rounded-lg bg-white px-7.5 pt-7.5 pb-4 text-lg">
          <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur
            vitae urna nec dolor ultrices cursus at quis magna. Nunc ac tortor
            tempor, malesuada ante non, pharetra elit. Nulla eleifend tellus
            vitae ex semper volutpat. Nunc condimentum metus velit, in hendrerit
            ligula consectetur sed. In euismod nulla varius convallis volutpat.
            Curabitur facilisis libero sed condimentum aliquam. Etiam eget erat
            iaculis, cursus ante eu, cursus arcu. Sed venenatis viverra egestas.
          </p>
        </div>
        <div
          class="col-span-4 rounded-lg bg-white px-4 pt-7 pb-4 text-gray-500"
        >
          <ul>
            <li
              v-for="i in 3"
              :key="i"
              class="mb-7.5 flex flex-row items-center text-lg"
            >
              <CheckIconComponent class="mr-1 h-[20px] w-[20px]" />
              <strong>Point clé n° {{ i }} : </strong> lorem ipsum dolor sit
              amet, consectetur adipiscing elit.
            </li>
          </ul>
        </div>
      </div>
      <div class="mt-10 mt-5 flex grid grid-cols-9 gap-4 text-lg">
        <div class="bloc-content col-span-5">
          <h3
            class="mb-[1.563rem] w-3/4 text-title-35 font-bold leading-9 text-primary"
          >
            Vos conditions négociées
          </h3>
          <p class="mb-10">
            Remise applicables sur XXXX / mentions légales conditions
          </p>
          <AutomobilePartnerComponent v-if="isAutomobile" />
          <ul v-else class="mx-7 list-disc">
            <li v-for="i in 4" :key="i" class="mb-5">
              Lorem <strong>ipsum dolor sit amet</strong>, consectetur
              adipiscing elit. <strong>Curabitur vitae </strong>urna nec dolor
              ultrices cursus at quis magna. Nunc ac tortor tempor,
              <strong>malesuada ante non</strong>, pharetra elit
            </li>
          </ul>
          <div class="flex justify-center">
            <ButtonComponent class="button-gradient mr-5 w-1/2">
              <DownloadIconComponent />
              Télécharger les conditions négociées
            </ButtonComponent>
          </div>
        </div>
        <div class="bloc-content col-span-4">
          <h3
            class="mb-[1.563rem] w-3/4 text-title-35 font-bold leading-9 text-primary"
          >
            Comment bénéficier des conditions ?
          </h3>
          <ButtonComponent class="button-gradient mb-7">
            <ArrowRightIconComponent /> Bénéficiez des conditions
          </ButtonComponent>
          <p class="mb-8">
            Process pour expliquer comment profiter des conditons ou toute autre
            information utile pour l'adhérent concernant la demande de
            rattachement Lorem ipsum dolor sit amet, consectetur adipiscing
            elit. Curabitur vitae urna nec dolor ultrices cursus at quis magna.
            Nunc ac tortor tempor, malesuada ante non, pharetra elit. Nulla
            eleifend tellus vitae ex semper volutpat.
          </p>
          <p>
            Nulla Lorem mollit cupidatat irure. Laborum magna nulla duis ullamco
            cillum dolor. Voluptate exercitation incididunt aliquip deserunt
            reprehenderit elit laborum. Amet minim mollit non deserunt ullamco
            est sit aliqua dolor do amet sint. Velit officia consequat duis enim
            velit mollit. Exercitation veniam consequat sunt nostrud amet.
          </p>
          <div class="mt-6 flex justify-between">
            <ButtonComponent class="button-secondary mr-5 w-1/2">
              <PhoneLightIconComponent />
              Contactez le fournisseur
            </ButtonComponent>
            <ButtonComponent class="button-secondary w-1/2">
              Bouton CTA
            </ButtonComponent>
          </div>
        </div>
      </div>
      <div class="flex justify-center">
        <CarouselListSharedComponent
          class="mx-auto mt-10 h-[443px] items-center rounded-xl bg-white px-4 py-4"
          :slides-per-view="1"
          :space-between="10"
          :pagination="true"
        >
          <SwiperSlide v-for="i in 5" :key="i">
            <div class="mt-5 flex grid grid-cols-9 gap-8">
              <div
                class="col-span-5 ml-10 flex h-[374px!important] items-center justify-center rounded-lg border py-8.5"
              >
                <img
                  :src="defaultImageFile"
                  alt="Picture"
                  class="object-cover"
                />
              </div>
              <div
                class="col-span-4 flex flex-col rounded-lg bg-white pr-12 text-lg text-gray-500"
              >
                <h3
                  class="mb-[1.563rem] w-3/4 text-title-35 font-bold leading-9 text-primary"
                >
                  Titre de la mise en avant du partenaire
                </h3>
                <p class="mb-5">
                  Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                  Curabitur vitae urna nec dolor ultrices cursus at quis magna.
                  Nunc ac tortor tempor, malesuada ante non, pharetra elit.
                  Nulla eleifend tellus vitae ex semper volutpat. Nunc
                  condimentum metus velit, in hendrerit ligula consectetur sed.
                  In euismod nulla varius convallis volutpat.
                </p>
                <p>
                  Amet minim mollit non deserunt ullamco est sit aliqua dolor do
                  amet sint. Velit officia consequat duis enim velit mollit.
                </p>

                <ButtonComponent class="button-secondary mt-6 w-1/2">
                  Bouton CTA
                </ButtonComponent>
              </div>
            </div>
          </SwiperSlide>
        </CarouselListSharedComponent>
      </div>

      <div class="mt-10 mt-5 flex grid h-[337px] grid-cols-3 gap-4">
        <div class="bloc-content col-span-2">
          <h3
            class="mb-[1.563rem] w-3/4 text-title-35 font-bold leading-9 text-primary"
          >
            Achats durables : <br />
            <span class="text-green">engagements RSE</span> du partenaire
          </h3>
          <p>
            Nos fournisseurs sont soumis à notre référentiel RSE, et obtiennent
            une note (affichée ici) qui constitue un des éléments clés de leur
            référencement. Nous avons co-construit ce référentiel sur-mesure
            avec nos adhérents, et fait valider par la société Tennaxia. Ce
            référentiel RSE est dédié aux PME et ETI. Vous pouvez également le
            soumettre à vos propres fournisseurs d’achats stratégiques pour
            progresser dans votre démarche RSE et créer ainsi un effet
            démultiplicateur qui touche de plus en plus de parties prenantes. Si
            vous souhaitez utiliser ce référentiel RSE, contactez-nous.
          </p>
        </div>
        <div class="rounded-lg bg-white pl-7.5 pt-11 text-lg text-gray-500">
          <ul>
            <li
              v-for="i in 3"
              :key="i"
              class="mb-16 flex flex-row items-center text-lg"
            >
              <CheckCircleIconComponent class="mr-2 h-[20px] w-[20px]" />
              <strong class="mr-1">Point clé n° {{ i }} : </strong> lorem ipsum
              dolor sit amet
            </li>
          </ul>
        </div>
      </div>
      <div class="mt-10">
        <h3 class="home-subtitle text-primary">
          Trouvez l'agence la plus <br />près de chez vous
        </h3>
      </div>
      <div class="my-10 mt-5 flex grid h-[379px] grid-cols-3 gap-4">
        <div class="col-span-2 rounded-lg bg-white"></div>
        <div class="bloc-content">
          <h3
            class="mb-[1.563rem] w-3/4 text-title-35 font-bold leading-9 text-primary"
          >
            En savoir plus sur ce partenaire
          </h3>
          <diV class="flex flex-col">
            <ButtonComponent
              class="button-white button-white-secondary mt-6 w-2/3"
            >
              <ArrowRightIconComponent /> Documentation générique
            </ButtonComponent>
            <ButtonComponent
              class="button-white button-white-secondary mt-6 w-2/3"
            >
              <ArrowRightIconComponent /> Flyer
            </ButtonComponent>
            <ButtonComponent
              class="button-white button-white-secondary mt-6 w-2/3"
            >
              <ArrowRightIconComponent /> ECT
            </ButtonComponent>
          </diV>
        </div>
      </div>

      <div class="mt-11">
        <h3 class="home-subtitle text-primary">
          Ces partenaires peuvent aussi
          <span class="text-gradient">vous intéresser</span>
        </h3>
      </div>
      <CarouselListSharedComponent
        class="mx-auto mt-10 h-[107px] items-center rounded-xl px-6 py-4 px-[20px!important]"
        :slides-per-view="8"
        :space-between="20"
      >
        <SwiperSlide
          v-for="i in 10"
          :key="i"
          class="flex items-center justify-center rounded-lg bg-white"
        >
          <img
            :src="defaultImageFile"
            alt="partenaire"
            class="h-[auto!important]"
          />
        </SwiperSlide>
      </CarouselListSharedComponent>
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import HeaderPartnerComponent from '@/vuejs/modules/partners/components/HeaderPartnerComponent.vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import { getImage } from '@/vuejs/services/utils'
import defaultImage from '@/vuejs/assets/img/default-image.png'
import { SwiperSlide } from 'swiper/vue'
import CheckIconComponent from '@/vuejs/modules/shared/icon/CheckIconComponent.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import PhoneLightIconComponent from '@/vuejs/modules/shared/icon/PhoneLightIconComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import DownloadIconComponent from '@/vuejs/modules/shared/icon/DownloadIconComponent.vue'
import CheckCircleIconComponent from '@/vuejs/modules/shared/icon/CheckCircleIconComponent.vue'
import { ref } from 'vue'
import AutomobilePartnerComponent from '@/vuejs/modules/partners/components/AutomobilePartnerComponent.vue'

const defaultImageFile = getImage(defaultImage)
const isAutomobile = ref<Boolean>(true)
</script>

<style scoped>
.bloc-content {
  @apply rounded-lg bg-white px-7.5 py-7.5 text-gray-500;
}
</style>
