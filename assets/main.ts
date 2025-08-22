import '@/style/main.scss'
import 'vue-universal-modal/dist/index.css'
import 'floating-vue/dist/style.css'

/** LEAFLET */
import L from 'leaflet'
globalThis.L = L
import 'leaflet/dist/leaflet.css'
import 'vue-leaflet-markercluster/dist/style.css'

import { createHead } from '@unhead/vue/client'
import { createApp } from 'vue'
import App from './vuejs/App.vue'
import router from '@/vuejs/router'
import VueUniversalModal from 'vue-universal-modal'
import FloatingVue from 'floating-vue'
import clickOutside from '@/vuejs/directives/click-outside'
import store from '@/vuejs/store'

const rootElements = document.querySelectorAll('.vue-app')
// Permet de gérer des multi-composants vue intégrées dans twig
// Chaque composant intégré dans twig doit posséder un attribut data-component
// qui indique le composant à charger, la logique se trouve dans App.vue
if (rootElements.length) {
  rootElements.forEach((rootElement) => {
    if ('component' in rootElement.dataset) {
      const component = rootElement.dataset.component
      const app = createApp(App, { component })
      app.use(store)
      app.mount(rootElement)
    }
  })
}

const twigEntryPoint = document.getElementById('app')
// Permet de lancer l'application compléte en mode SPA
if (twigEntryPoint) {
  // Lancement de l'App compléte depuis un point d'entrée twig
  const app = createApp(App)
  const head = createHead()
  app.use(store).directive('click-outside', clickOutside)
  app.use(router)
  app.use(head)
  app.use(VueUniversalModal, { teleportTarget: '#modals' })
  app.use(FloatingVue)
  app.mount('#app')
}
