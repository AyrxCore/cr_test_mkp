import { ref } from 'vue'
import { getImage } from '@/vuejs/services/utils'
import imgMaps from '@/vuejs/assets/img/samples/img-maps.png'
import imgCentral from '@/vuejs/assets/img/samples/img-central.png'
import imgMagazine from '@/vuejs/assets/img/samples/img-magazine.png'

import imgMapsLandscape from '@/vuejs/assets/img/samples/img-maps-landscape.png'
import imgCentralLandscape from '@/vuejs/assets/img/samples/actu-centrale.png'
import imgMagazineLandscape from '@/vuejs/assets/img/samples/img-magazine-landscape.png'

export const contenusExpert = ref([
  {
    img: getImage(imgMaps),
    img_landscape: getImage(imgMapsLandscape),
    title: 'Loi montagne : êtes-vous concernés ? ',
    btnNam: 'Actualités',
    description: 'A partir du 1er novembre 2022, la loi Montagne...',
    date: '19/09/2022',
    categorie: {
      id: 'actualites',
      name: 'Actualités',
      color: 'bg-secondary',
    },
  },
  {
    img: getImage(imgCentral),
    img_landscape: getImage(imgCentralLandscape),
    title: 'Tout savoir sur le décret tertiaire',
    btnNam: 'Actualités',
    description: 'Le décret tertiaire impose une réduction \nde consommation...',
    date: '19/09/2022',
    categorie: {
      id: 'actualites',
      name: 'Actualités',
      color: 'bg-secondary',
    },
  },
  {
    img: getImage(imgMagazine),
    img_landscape: getImage(imgMagazineLandscape),
    title: 'Comment entretenir votre véhicule ? ',
    btnNam: 'Conseil',
    description: 'Les équipes de QANTIS ont conçu pour vous un guide...',
    date: '19/09/2022',
    categorie: {
      id: 'conseil',
      name: 'Conseil',
      color: 'bg-primary',
    },
  },
])
