<template>
  <div
    class="footer"
    :style="{
      color: betterTextColor('primary'),
    }"
  >
    <div
      class="second-part md:px-15 mx-5 flex flex-wrap justify-between gap-5 py-10 md:m-auto md:px-10 lg:flex-nowrap lg:px-20"
    >
      <div class="mb-4 sm:w-full md:w-5/12 md:flex-1 lg:w-1/5">
        <h3 class="mb-6 md:mb-4 lg:mb-7">La marketplace QANTIS</h3>
        <p>
          Depuis 2001, QANTIS accompagne les entreprises françaises dans leur
          performance et leur croissance durable en s'appuyant sur 3 moteurs :
          la centrale d'achat, l'expertise humaine et la marketplace.
        </p>
      </div>
      <div class="mb-4 sm:w-full md:w-5/12 lg:w-1/5">
        <h3 class="mb-6 lg:mb-7">À propos</h3>
        <ul>
          <li>
            <RouterLink
              :to="{ name: PageList.CONTACT_PAGE }"
              @click="gtmEvent('click_footer_contact')"
            >
              Nous contacter
            </RouterLink>
          </li>
          <li>
            <a
              target="_blank"
              :href="currentChannel.documents.generalTermsOfUse"
              @click="gtmEvent('click_footer_cgu')"
            >
              Conditions générales d'utilisation
            </a>
          </li>
          <li>
            <a
              target="_blank"
              :href="currentChannel.documents.legalTerms"
              @click="gtmEvent('click_footer_mentions_legales')"
            >
              Mentions légales
            </a>
          </li>
          <li>
            <a
              target="_blank"
              :href="currentChannel.documents.privacyPolicy"
              @click="gtmEvent('click_footer_politique_confidentialite')"
            >
              Politique de confidentialité
            </a>
          </li>
        </ul>
      </div>
      <div class="mb-4 sm:w-full md:mr-4 md:w-5/12 lg:w-1/5">
        <h3 class="mb-6 lg:mb-7">Votre espace adhérents</h3>
        <ul>
          <li>
            <RouterLink
              :to="{ name: AccountPageList.ACCOUNT }"
              @click="gtmEvent('click_footer_account')"
            >
              Mon compte
            </RouterLink>
          </li>
          <li>
            <RouterLink
              :to="{ name: AccountPageList.ORDERS }"
              @click="gtmEvent('click_footer_mes_commandes')"
            >
              Mes commandes
            </RouterLink>
          </li>
        </ul>
      </div>
      <div class="mb-4 sm:w-full md:w-5/12 lg:w-1/5">
        <div class="flex flex-col md:flex-row md:justify-between lg:flex-col">
          <div class="md:mt-0">
            <h3 class="mb-6 lg:mb-7">Paiement sécurisé par&nbsp;:</h3>
            <img
              :src="lemonwayLogoImg"
              alt="header"
              class="logo-lemway mt-[0.5rem]"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

import lemonwayLogo from '@/vuejs/assets/img/lemonway_footer_logo.png'
import { betterTextColor, getImage } from '@/vuejs/services/utils'
import { useCategoryStore } from '@/vuejs/stores/category'
import { useUserStore } from '@/vuejs/stores/user'
import { useChannelStore } from '@/vuejs/stores/channel'
import { AccountPageList } from '@/vuejs/router/pages-list'
import { PageList } from '@/vuejs/router'
import { buildStandardGtmData, gtmMixinPushEvent } from '@/vuejs/services/gtm'

const lemonwayLogoImg = getImage(lemonwayLogo)

const categoryStore = useCategoryStore()
const userStore = useUserStore()
const channelStore = useChannelStore()

const categories = computed(() => {
  return categoryStore.categories
})

const currentChannel = channelStore.currentChannel

const gtmEvent = (eventName: string) => {
  gtmMixinPushEvent(
    eventName,
    buildStandardGtmData(userStore.user['@id'], currentChannel.name),
  )
}
</script>

<style lang="scss">
.three-column-list {
  @apply m-0 columns-1 p-0 md:columns-2 lg:columns-3;
}

.footer {
  background-color: var(--primary-color);
  width: 100%;
}

.footer h3 {
  font-size: 24px;
  font-weight: 700;
  line-height: 28px;
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

.footer .logo-lemway {
  height: 48px;
  width: 209px;
  top: 289px;
}

.footer ul li a:hover {
  @apply border-b-2 border-secondary;
}

.footer .social-network-logo div {
  @apply float-left mr-2.5 box-border h-[40px] w-[40px] rounded-full border border-white p-2.5;
}
</style>
