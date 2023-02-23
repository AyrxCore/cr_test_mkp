import { getImage } from '@/vuejs/services/utils'
import prod1 from '@/vuejs/assets/img/demo/07651.jpg'
import prod2 from '@/vuejs/assets/img/demo/18061.jpg'
import prod3 from '@/vuejs/assets/img/demo/jj03541.jpg'
import prod4 from '@/vuejs/assets/img/demo/122021.jpg'


import prod5 from '@/vuejs/assets/img/demo/16275.jpg'
import prod6 from '@/vuejs/assets/img/demo/80877.jpg'
import prod7 from '@/vuejs/assets/img/demo/72821.jpg'
import prod8 from '@/vuejs/assets/img/demo/02890.jpg'

import imgFrn1 from '@/vuejs/assets/img/demo/alda.png'
import imgFrn2 from '@/vuejs/assets/img/samples/berner.png'
import imgFrn3 from '@/vuejs/assets/img/samples/mieko.png'
import imgFrn4 from '@/vuejs/assets/img/demo/hedis.png'

import { ref } from 'vue'

export const status = ref({
  not_activated: 'NOT_ACTIVATED',
  pending: 'PENDING',
  activated: 'ACTIVATED',
})

export const productsTopVenteHomepage = [
  {
    name: 'Souris optique filaire bleu',
    description: '5 boutons (latéraux pour la navigatio…',
    partner: '',
    price_line_through: '9,72',
    price: '6,32',
    image: getImage(prod1),
    fournisseur_image: getImage(imgFrn1),
  },
  {
    name: 'BPT-RD 10 QC PERCEUSE REVERS.',
    description: 'Perceuse réversible pneumatique man…',
    partner: 'BERNER',
    price_line_through: '276,03',
    price: '193,22',
    image: getImage(prod2),
    fournisseur_image: getImage(imgFrn2),
  },
  {
    name: 'Suspension WOODY 4xE27',
    description: 'FINITION BLANCHE ø500mm IP20',
    partner: '',
    price_line_through: '',
    price: '129,17',
    image: getImage(prod3),
    fournisseur_image: getImage(imgFrn3),
  },
  {
    name: 'Bobine Essuyage - Colis 2',
    description: 'Bobine 1000f blanc 2p lisse',
    partner: '',
    price_line_through: '26,07',
    price: '11,65',
    image: getImage(prod4),
    fournisseur_image: getImage(imgFrn4),
  },
]

export const productsSimilaire = [
  {
    name: 'Clavier et souris Keyouest filaire',
    description: 'Clavier azerty : touches souples…',
    partner: 'ALDA MAJUSCULE',
    price_line_through: '14,20',
    price: '9,23',
    image: getImage(prod5),
    fournisseur_image: getImage(imgFrn1),
  },
  {
    name: 'Cassette nylon à la marque Oki',
    description: 'Cassette nylon à la marque Oki…',
    partner: 'ALDA MAJUSCULE',
    price_line_through: '34,60',
    price: '22,49',
    image: getImage(prod6),
    fournisseur_image: getImage(imgFrn1),
  },
  {
    name: 'Clé USB Rainbow 4 Go',
    description: 'Vitesse maximale de lecture : 28 Mo/s…',
    partner: '',
    price_line_through: '5,38',
    price: '3,50',
    image: getImage(prod7),
    fournisseur_image: getImage(imgFrn1),
  },
  {
    name: 'Pavé numérique USB filaire noir',
    description: 'Nombre de touches : 23. Cordon de…',
    partner: '',
    price_line_through: '11,28',
    price: '7,33',
    image: getImage(prod8),
    fournisseur_image: getImage(imgFrn1),
  },
]
