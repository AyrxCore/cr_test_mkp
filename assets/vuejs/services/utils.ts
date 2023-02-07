import { ref } from 'vue'

export function getImage(urlImage: string): string {
  return new URL(urlImage, import.meta.url).href
}

export function openInNewTab(url) {
  window.open(url, '_blank', 'noreferrer')
}

export const HOME_TOP_VENTE_PROPERTY = {
  property_id: '176',
  value: '8389'
}
export const HOME_SELECTION_PROPERTY = {
  property_id: '176',
  value: '8388'
}
export const PRODUCT_ACCORD_PROPERTY = {
  property_id: '169',
  value: 'Oui'
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
