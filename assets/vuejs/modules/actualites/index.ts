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
    img_portrait: getImage(imgMaps),
    img_landscape: getImage(imgMapsLandscape),
    title: 'Loi montagne : êtes-vous concernés ? ',
    teaser: 'Le décret tertiaire impose une réduction \nde consommation...',
    description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac sem at enim convallis consectetur quis sed diam. Curabitur consequat sagittis tempus. Nulla mollis felis erat, non tincidunt ligula mattis vulputate. Aenean cursus dictum tempor. Proin sit amet quam in diam tempor cursus. Curabitur aliquet ut odio at vehicula. Donec tristique gravida tristique. Sed ullamcorper interdum vestibulum. Proin eu tincidunt justo.\n' +
      '\n' +
      'Curabitur turpis lectus, suscipit et velit non, ornare facilisis justo. In maximus tempor est, sodales congue dui accumsan ut. In bibendum mi nunc, ac aliquet eros placerat eu. Nunc dictum ipsum sed cursus laoreet. Vestibulum tincidunt sapien dolor, sit amet tempus purus posuere quis. Praesent tempus risus ligula, eget rhoncus velit tempus id. Fusce placerat, odio non auctor lacinia, mi libero varius diam, id sagittis ipsum tellus ac erat. Maecenas quis erat maximus, pharetra metus eget, egestas leo. Aliquam eu tortor blandit, dignissim nibh in, elementum elit.',
    date: '19/09/2022',
    categorie_name: 'Actualités',
    categorie_color: '#9553ff',
  },
  {
    img_portrait: getImage(imgCentral),
    img_landscape: getImage(imgCentralLandscape),
    title: 'Tout savoir sur le décret tertiaire',
    teaser: 'Le décret tertiaire impose une réduction \nde consommation...',
    description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac sem at enim convallis consectetur quis sed diam. Curabitur consequat sagittis tempus. Nulla mollis felis erat, non tincidunt ligula mattis vulputate. Aenean cursus dictum tempor. Proin sit amet quam in diam tempor cursus. Curabitur aliquet ut odio at vehicula. Donec tristique gravida tristique. Sed ullamcorper interdum vestibulum. Proin eu tincidunt justo.\n' +
      '\n' +
      'Curabitur turpis lectus, suscipit et velit non, ornare facilisis justo. In maximus tempor est, sodales congue dui accumsan ut. In bibendum mi nunc, ac aliquet eros placerat eu. Nunc dictum ipsum sed cursus laoreet. Vestibulum tincidunt sapien dolor, sit amet tempus purus posuere quis. Praesent tempus risus ligula, eget rhoncus velit tempus id. Fusce placerat, odio non auctor lacinia, mi libero varius diam, id sagittis ipsum tellus ac erat. Maecenas quis erat maximus, pharetra metus eget, egestas leo. Aliquam eu tortor blandit, dignissim nibh in, elementum elit.',
    date: '19/09/2022',
    categorie_name: 'Actualités',
    categorie_color: '#9553FF',
  },
  {
    img_portrait: getImage(imgMagazine),
    img_landscape: getImage(imgMagazineLandscape),
    title: 'Comment entretenir votre véhicule ? ',
    teaser: 'Le décret tertiaire impose une réduction \nde consommation...',
    description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac sem at enim convallis consectetur quis sed diam. Curabitur consequat sagittis tempus. Nulla mollis felis erat, non tincidunt ligula mattis vulputate. Aenean cursus dictum tempor. Proin sit amet quam in diam tempor cursus. Curabitur aliquet ut odio at vehicula. Donec tristique gravida tristique. Sed ullamcorper interdum vestibulum. Proin eu tincidunt justo.\n' +
      '\n' +
      'Curabitur turpis lectus, suscipit et velit non, ornare facilisis justo. In maximus tempor est, sodales congue dui accumsan ut. In bibendum mi nunc, ac aliquet eros placerat eu. Nunc dictum ipsum sed cursus laoreet. Vestibulum tincidunt sapien dolor, sit amet tempus purus posuere quis. Praesent tempus risus ligula, eget rhoncus velit tempus id. Fusce placerat, odio non auctor lacinia, mi libero varius diam, id sagittis ipsum tellus ac erat. Maecenas quis erat maximus, pharetra metus eget, egestas leo. Aliquam eu tortor blandit, dignissim nibh in, elementum elit.',
    date: '19/09/2022',
    categorie_name: 'Conseil',
    categorie_color: '#050056',
  },
])
