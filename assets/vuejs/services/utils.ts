import { ref } from 'vue'
import { Address } from '../types/Address'
import imgDefault from '@/vuejs/assets/img/default-image.png'

export function getImage(urlImage: string): string {
  return new URL(urlImage, import.meta.url).href
}

export function openInNewTab(url) {
  window.open(url, '_blank', 'noreferrer')
}

export function getUpplerImage(path: string | null) {
  return path !== null ? path : getImage(imgDefault)
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

export function formatPrice(price: number): string {
  return price.toLocaleString('fr', {
    minimumFractionDigits: 2,
  })
}

export function formatAddress(address: Address): string {
  if (!address) return null
  return `${!address.company ? '' : address.company + ', '} ${address.street} ${
    address.postcode
  } ${address.city}`
}
