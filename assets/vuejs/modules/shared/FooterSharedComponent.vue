<template>
  <div class="footer mt-16">
    <div class="bloc-contact-footer flex w-full flex-row">
      <div
        class="flex w-[100%] max-w-screen-2xl flex-col pt-5 lg:m-auto lg:my-4 lg:grid lg:grid-cols-3 lg:gap-4"
      >
        <div class="flex flex-col text-lg">
          <div class="items-center">
            <div class="flex items-center">
              <PhoneIconComponent class="mr-4 h-[24px] w-[24px]" />
              <div class="text-sm md:text-base lg:text-lg">
                {{ PHONE_ANIMATION }}
              </div>
            </div>
            <div class="mt-2 flex items-center">
              <MailIconComponent class="mr-4 h-[24px] w-[24px]" />
              <div class="text-sm md:text-base lg:text-lg">
                {{ MAIL_ANIMATION }}
              </div>
            </div>
            <div class="mt-2 flex">
              <CalendarCheckIconComponent class="mr-4 h-[24px] w-[24px]" />
              <div class="text-sm md:text-base lg:text-lg">
                Rendez-vous avec votre animateur
              </div>
            </div>
          </div>
        </div>
        <div class="col-span-2">
          <h3>Êtes-vous abonné à notre newsletter ?</h3>
          <p class="mb-4 text-sm md:text-base lg:text-lg">
            Recevez les actualités de nos partenaires, leurs produits et
            services
          </p>
          <InputButtonComponent
            placeholder="Votre adresse email"
            :btn-color="'bg-qantis'"
          >
            <span class="text-sm md:text-base lg:text-lg"> S'inscrire</span>
          </InputButtonComponent>
          <p class="mt-4 text-sm">
            Votre adresse email sera uniquement utilisée pour vous envoyer nos
            newsletters. Vous pourrez vous désabonner à tout moment via le lien
            intégré dans la newsletter. En savoir plus sur la gestion de vos
            données et vos droits
          </p>
        </div>
      </div>
    </div>
    <div
      class="second-part mx-5 my-2 flex max-w-screen-2xl flex-col pt-10 md:m-auto lg:my-4 lg:grid lg:grid-flow-col lg:grid-rows-3 lg:gap-4"
    >
      <div class="row-span-3 md:px-6 lg:px-3">
        <div class="flex flex-col md:flex-row md:justify-between lg:flex-col">
          <img :src="qantisLogoImg" alt="header" class="logo-qantis" />
          <img
            :src="ecologieLogoImg"
            alt="header"
            class="logo-ecolo mt-[3rem] md:mt-0 lg:mt-[3rem]"
          />
          <div class="mt-12 md:mt-0 lg:mt-12">
            <p>Paiment sécurisé par :</p>
            <img
              :src="lemonwayLogoImg"
              alt="header"
              class="logo-lemway mt-[0.5rem]"
            />
          </div>
        </div>
        <div
          class="mt-[3.5rem] flex w-[241px] flex-col md:w-full md:flex-row md:items-center lg:w-[241px] lg:flex-col lg:items-start"
        >
          <p class="mb-5 w-full md:w-3/6 lg:w-full">
            Retrouvez l'actualité économique de la Communauté QANTIS sur nos
            réseaux :
          </p>
          <div class="social-network-logo mb-2 inline">
            <div><img :src="linkedInLogoImg" alt="Linkedin" /></div>
            <div><img :src="facebookLogoImg" alt="Facebook" /></div>
            <div><img :src="twitterLogoImg" alt="Twitter" /></div>
            <div>
              <img :src="youtubeLogoImg" alt="Youtube" class="mt-0.5" />
            </div>
          </div>
        </div>
      </div>
      <div
        class="mt-6 flex flex-col md:col-span-2 md:grid md:grid-cols-3 md:gap-4 md:px-6 lg:mt-0 lg:px-3"
      >
        <div class="px-2 md:px-0 lg:px-2">
          <h3 class="mb-7 md:mb-4 lg:mb-7">Votre espace adhérents</h3>
          <ul>
            <li>
              <RouterLink :to="{ name: AccountPageList.ACCOUNT }">
                Mon compte
              </RouterLink>
            </li>
            <!-- <li><a href="#">Mes économies</a></li>
            <li>
              <a href="/app/account/orders-history">Historique de commandes</a>
            </li>
            <li><a href="#">Factures</a></li> -->
          </ul>
        </div>
        <div class="mt-6 px-2 md:mt-0 md:px-0 lg:px-2">
          <h3 class="mb-7 md:mb-10 lg:mb-7">À propos</h3>
          <ul>
            <li><a href="#">Nous contacter</a></li>
            <li><a href="#">Conditions d'utilisation</a></li>
            <li>
              <a href="#">Conditions générales <br />d'utilisation Lemon Way</a>
            </li>
            <li><a href="#">Mentions légales</a></li>
            <li><a href="#">Cookies</a></li>
          </ul>
        </div>
        <div class="mt-6 px-2 md:mt-0 md:px-0 lg:px-2">
          <h3 class="mb-7 md:mb-4 lg:mb-7">La marketplace QANTIS</h3>
          <p>
            Depuis 21 ans, QANTIS accompagne les entreprises françaises dans
            leurs performances et leur croissance durable en s'appuyant sur 3
            moteurs : la centrale d'achat, l'expertise humaine et la
            marketplace.
          </p>
        </div>
      </div>
      <div class="mt-4 md:col-span-2 md:row-span-2">
        <h3 class="mb-5 px-2 md:mb-4 lg:mb-7">Nos catégories d'achats</h3>
        <ul class="three-column-list">
          <li v-for="category in categories" :key="category.id" class="px-2">
            <RouterLink
              :to="{
                name: ProductPageList.PRODUCTS,
                query: { category: category.id, page: 1 },
              }"
              replace
            >
              {{ category.name }}
            </RouterLink>
          </li>
        </ul>
      </div>
    </div>
    <hr class="m-auto w-11/12 border md:max-w-screen-2xl" />
    <div
      class="m-auto mt-7 py-10 px-5 text-left text-sm md:max-w-screen-2xl md:text-center lg:mt-10 lg:px-0 lg:text-lg"
    >
      © QANTIS. Tous droits réservés - QANTIS, 185, allée des Cyprès, 69760
      LIMONEST, FRANCE
    </div>
  </div>
</template>

<script lang="ts" setup>
import { getImage } from '@/vuejs/services/utils'
import qantisLogo from '@/vuejs/assets/img/logo_qantis_footer.png'
import ecologieLogo from '@/vuejs/assets/img/coq_vert_footer_logo.png'
import lemonwayLogo from '@/vuejs/assets/img/lemonway_footer_logo.png'
import linkedInLogo from '@/vuejs/assets/img/Pictos/linkedin_logo.png'
import twitterLogo from '@/vuejs/assets/img/Pictos/twitter_logo.png'
import youtubeLogo from '@/vuejs/assets/img/Pictos/youtube_logo.png'
import facebookLogo from '@/vuejs/assets/img/Pictos/facebook_social_logo.png'
import InputButtonComponent from '@/vuejs/modules/shared/InputButtonComponent.vue'
import MailIconComponent from '@/vuejs/modules/shared/icon/MailIconComponent.vue'
import PhoneIconComponent from '@/vuejs/modules/shared/icon/PhoneIconComponent.vue'
import CalendarCheckIconComponent from '@/vuejs/modules/shared/icon/CalendarCheckIconComponent.vue'
import { MAIL_ANIMATION, PHONE_ANIMATION } from '../../services/const'
import { useCategoryStore } from '@/vuejs/stores/category'
import { computed } from 'vue'
import { AccountPageList, ProductPageList } from '@/vuejs/router/pages-list'

const qantisLogoImg = getImage(qantisLogo)
const ecologieLogoImg = getImage(ecologieLogo)
const lemonwayLogoImg = getImage(lemonwayLogo)

const linkedInLogoImg = getImage(linkedInLogo)
const twitterLogoImg = getImage(twitterLogo)
const youtubeLogoImg = getImage(youtubeLogo)
const facebookLogoImg = getImage(facebookLogo)

const categoryStore = useCategoryStore()

const categories = computed(() => {
  return categoryStore.categories
})
</script>

<style lang="scss">
.three-column-list {
  @apply m-0 columns-1 p-0 md:columns-2 lg:columns-3;
}

@import 'assets/style/_variables.scss';
.footer {
  background-color: $primary;
  color: #ffffff;
  width: 100%;
}

.footer h3 {
  font-size: 22px;
  font-weight: 700;
  line-height: 26px;
  text-align: left;
}

.footer .second-part p,
.footer li {
  @apply text-left text-sm font-normal leading-6 md:text-base xl:text-lg;
}

.bloc-contact-footer {
  @apply h-auto bg-secondary;
}

.bloc-contact-footer > div > div {
  @apply m-5 sm:mx-12 xl:mx-24;
}

.footer .logo-qantis {
  height: 98px;
  width: 202px;
}

.footer .logo-ecolo {
  height: 69px;
  width: 202px;
  top: 144px;
}

.footer .logo-lemway {
  height: 48px;
  width: 209px;
  top: 289px;
}

.footer ul li a:hover {
  border-bottom: 2px solid $secondary;
}

.footer .social-network-logo div {
  @apply float-left mr-2.5 box-border h-[40px] w-[40px] rounded-full border border-white p-2.5;
}
</style>
