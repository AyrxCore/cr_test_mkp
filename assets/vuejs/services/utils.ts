import { ref } from 'vue'

export function getImage(urlImage: string): string {
  return new URL(urlImage, import.meta.url).href
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
