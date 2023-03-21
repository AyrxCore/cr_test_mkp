import '@/style/main.scss'
import 'vue-universal-modal/dist/index.css'
import { createHead } from '@vueuse/head'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './vuejs/App.vue'
import router from '@/vuejs/router'
import VueUniversalModal from 'vue-universal-modal'

import clickOutside from '@/vuejs/directives/click-outside'

const rootElements = document.querySelectorAll('.vue-app')
// Permet de gérer des multi-composants vue intégrées dans twig
// Chaque composant intégré dans twig doit posséder un attribut data-component
// qui indique le composant à charger, la logique se trouve dans App.vue
if (rootElements.length) {
  rootElements.forEach((rootElement) => {
    if ('component' in rootElement.dataset) {
      const component = rootElement.dataset.component
      const app = createApp(App, { component })
      app.use(createPinia())
      app.mount(rootElement)
    }
  })
}

// Permet de lancer l'application compléte en mode SPA
if (document.getElementById('app')) {
  // Lancement de l'App compléte depuis un point d'entré twig
  const app = createApp(App)
  const head = createHead()
  app.use(router)
  app.use(head)
  app.use(createPinia()).directive('click-outside', clickOutside)
  app.use(VueUniversalModal, {
    teleportTarget: '#modals',
  })
  app.mount('#app')
}
