import { ref } from 'vue'
import imgDefault from '@/vuejs/assets/img/default-image.png'

export function getImage(urlImage: string): string {
  return new URL(urlImage, import.meta.url).href
}

export function openInNewTab(url) {
  window.open(url, '_blank', 'noreferrer')
}

export const IMG_PATH='https://uppler-platform-quantis.s3.eu-west-3.amazonaws.com/image/'

export function getUpplerImage( path: string|null)
{

  if (path) {
    return IMG_PATH + path
  } else {
    return getImage(imgDefault)
  }
}


export const HOME_TOP_VENTE_PROPERTY = {
  property_id: '217',
  value: '5369'
}
export const HOME_SELECTION_PROPERTY = {
  property_id: '217',
  value: '5368'
}

export const HOME_ACCORD_CADRE_PROPERTY = {
  property_id: '217',
  value: '5367'
}

export const listCategories = ref<string[]>([
  'Avantages salariés',
  'Certifications',
  'Chimie des bâtiments et des process',
  'Conseils',
  'Eclairage',
  'Emballages et expédition',
  'Energie',
  'Equipements atelier',
  'Equipements de protection individuelle (EPI)',
  'Formation',
  'Fournitures de bureau',
  'Gestion des déchets',
  "Hygiène et produits d'entretien",
  'Informatique et burautique',
  'Location de matériels',
  'Matériaux',
  'Mobilier et agencement',
  'Outillage',
  'Peinture',
  'Quincaillerie',
  'Rubans et adhésifs',
  'Sécurité, maintenance et entretien des locaux',
  'Services généraux',
  'Téléphonie',
  'Véhicules',
])
