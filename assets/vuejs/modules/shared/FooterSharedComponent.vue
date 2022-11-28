<template>
  <div class="footer mt-16">
    <div class="bloc-contact-footer flex w-full flex-row">
      <div
        class="m-auto my-4 grid w-[100%] max-w-screen-2xl grid-cols-3 gap-4 pt-5"
      >
        <div class="flex text-lg">
          <div>
            <div><PhoneIconComponent class="fa fa-phone mr-2 text-lg" /></div>
            <div class="mt-3"><MailIconComponent class="mr-2 text-lg" /></div>
            <div class="mt-3">
              <CalendarCheckIconComponent class="fa fa-phone mr-2 text-lg" />
            </div>
          </div>
          <div>
            <div>04 37 65 06 61</div>
            <div class="mt-3">animation@qantis.co</div>
            <div class="mt-3">Rendez-vous avec votre animateur</div>
          </div>
        </div>
        <div class="col-span-2">
          <h3>Etes-vous abonné à notre newsletter ?</h3>
          <p class="mb-4 text-lg">
            Recevez les actualités de nos partenaires, leurs produits et
            services
          </p>
          <input-button-component
            placeholder="Votre adresse email"
            :btn-color="'bg-qantis'"
          >
            S'inscrire
          </input-button-component>
          <p class="mt-4 text-[14px]">
            Votre adresse email sera uniquement utilisée pour vous envoyer nos
            newsletters. Vous pourrez vous désabonner à tout moment via le lien
            intégré dans la newsletter. En savoir plus sur la gestion de vos
            données et vos droits
          </p>
        </div>
      </div>
    </div>
    <div
      class="second-part m-auto my-4 grid max-w-screen-2xl grid-flow-col grid-rows-3 gap-4 pt-10"
    >
      <div class="row-span-3 px-3">
        <img :src="qantisLogoImg" alt="header" class="logo-qantis" />
        <img :src="ecologieLogoImg" alt="header" class="logo-ecolo mt-[3rem]" />
        <p>Paiment sécurisé par :</p>
        <img
          :src="lemonwayLogoImg"
          alt="header"
          class="logo-lemway mt-[0.5rem]"
        />
        <p class="mt-[3.5rem] mb-5">
          Retrouvez l'actualité économique <br />de la Communauté QANTIS sur
          <br />nos réseaux :
        </p>
        <div class="social-network-logo inline">
          <div><img :src="linkedInLogoImg" alt="Linkedin" /></div>
          <div><img :src="facebookLogoImg" alt="Facebook" /></div>
          <div><img :src="twitterLogoImg" alt="Twitter" /></div>
          <div><img :src="youtubeLogoImg" alt="Youtube" class="mt-0.5" /></div>
        </div>
      </div>
      <div class="col-span-2 grid grid-cols-3 gap-4">
        <div class="px-2">
          <h3 class="mb-7">Votre espace adhérents</h3>
          <ul>
            <li><a href="#">Mon compte</a></li>
            <li><a href="#">Mes économies</a></li>
            <li><a href="#">Historique de commandes</a></li>
            <li><a href="#">Factures</a></li>
          </ul>
        </div>
        <div class="px-2">
          <h3 class="mb-7">A propos</h3>
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
        <div class="px-2">
          <h3 class="mb-7">La marketplace QANTIS</h3>
          <p>
            Depuis 21 ans, QANTIS accompagne les entreprises françaises dans
            leurs performances et leur croissance durable en s'appuyant sur 3
            moteurs : la centrale d'achat, l'expertise humaine et la
            marketplace.
          </p>
        </div>
      </div>
      <div class="col-span-2 row-span-2">
        <div class="m-auto my-4 grid grid-cols-3 gap-4 pt-10">
          <div class="px-2">
            <h3 class="mb-7">Votre espace adhérents</h3>
            <ul>
              <li><a href="#">Avantages salariés</a></li>
              <li><a href="#">Certifications</a></li>
              <li><a href="#">Chimie des batiments et des process</a></li>
              <li><a href="#">Conseils</a></li>
              <li><a href="#">Eclairage</a></li>
              <li><a href="#">Energie</a></li>
              <li><a href="#">Equipements ateliers</a></li>
              <li>
                <a href="#"
                  >Equipements de protections <br />
                  individuelle</a
                >
              </li>
            </ul>
          </div>
          <div class="px-2">
            <ul class="mt-[3rem] mb-7">
              <li><a href="#">Formation</a></li>
              <li><a href="#">Fourniture de bureaux</a></li>
              <li><a href="#">Gestion des déchets</a></li>
              <li><a href="#">Hygiène et produits d'entretien</a></li>
              <li><a href="#">Informatique et bureautique</a></li>
              <li><a href="#">Interim</a></li>
              <li><a href="#">Location de matériels</a></li>
              <li><a href="#">Matériaux</a></li>
              <li><a href="#">Mobilier et agencement</a></li>
            </ul>
          </div>
          <div class="px-2">
            <ul class="mt-[3rem] mb-7">
              <li><a href="#">Outillage</a></li>
              <li><a href="#">Peinture</a></li>
              <li><a href="#">Quincaillerie</a></li>
              <li><a href="#">Rubans et adhésifs</a></li>
              <li>
                <a href="#"
                  >Sécurité, maintenance et <br />entretien des locaux</a
                >
              </li>
              <li><a href="#">Services généraux</a></li>
              <li><a href="#">Téléphonie</a></li>
              <li><a href="#">Véhicules</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div
      class="text-cotext m-auto mt-10 max-w-screen-2xl border-t-2 border-white pt-10 pb-10 text-center"
    >
      © QANTIS. Tous droits réservés - QANTIS, 185, allée des Cyprès, 69760
      LIMONEST, FRANCE
    </div>
  </div>
</template>

<script lang="ts" setup>
import { useUserStore } from '@/vuejs/stores/user'
import { storeToRefs } from 'pinia'
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

const userStore = useUserStore()
const { user } = storeToRefs(userStore)
const qantisLogoImg = getImage(qantisLogo)
const ecologieLogoImg = getImage(ecologieLogo)
const lemonwayLogoImg = getImage(lemonwayLogo)

const linkedInLogoImg = getImage(linkedInLogo)
const twitterLogoImg = getImage(twitterLogo)
const youtubeLogoImg = getImage(youtubeLogo)
const facebookLogoImg = getImage(facebookLogo)
</script>

<style lang="scss">
@import 'assets/style/_variables.scss';
.footer {
  background-color: $primary;
  color: #ffffff;
  width: 100%;
}

.footer h3 {
  font-family: CoText, sans-serif;
  font-size: 22px;
  font-weight: 700;
  line-height: 26px;
  text-align: left;
}

.footer .second-part p,
.footer li {
  font-family: CoText, sans-serif;
  font-size: 18px;
  font-weight: 400;
  line-height: 25px;
  letter-spacing: 0em;
  text-align: left;
}

.bloc-contact-footer {
  height: 254px;
  background: $secondary;
  font-family: CoText, sans-serif;
}

.bloc-contact-footer > div > div {
  margin-right: 6rem;
  margin-left: 6rem;
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
  box-sizing: border-box;
  width: 40px;
  height: 40px;
  border: 1px solid #ffffff;
  border-radius: 20px;
  float: left;
  margin-right: 10px;
  padding: 10px;
}
</style>
