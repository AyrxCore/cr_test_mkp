/**
 * Types pour les valeurs d'options de produits
 */
export type OptionValue =
  | string
  | { code: string; label: string }
  | Record<string, unknown>

/**
 * Type guard pour vérifier si une valeur a une propriété string
 */
const hasStringProperty = (
  obj: unknown,
  key: string,
): obj is Record<string, unknown> => {
  return (
    typeof obj === 'object' &&
    obj !== null &&
    key in obj &&
    typeof (obj as Record<string, unknown>)[key] === 'string'
  )
}

/**
 * Extrait une propriété string non-vide d'un objet
 */
const getStringProperty = (
  obj: Record<string, unknown>,
  key: string,
): string | null => {
  if (hasStringProperty(obj, key)) {
    const value = (obj as Record<string, string>)[key]
    const trimmed = value.trim()
    return trimmed || null
  }
  return null
}

/**
 * Convertit une valeur d'option en string pour v-model (stringify si objet)
 * Gère null/undefined proprement
 */
export const getOptionValue = (value: OptionValue): string => {
  if (value == null) return ''

  if (typeof value === 'object') {
    return JSON.stringify(value)
  }

  return String(value)
}

/**
 * Obtient le label à afficher pour une valeur d'option
 * Priorité : propriété 'label' > propriété 'name' > toString() > JSON.stringify
 */
export const getOptionLabel = (value: OptionValue): string => {
  if (value == null) return ''

  if (typeof value === 'object' && value !== null) {
    // Tentative d'extraction de propriétés communes
    const label = getStringProperty(value, 'label')
    if (label) return label

    const name = getStringProperty(value, 'name')
    if (name) return name

    // Pour les objets avec toString personnalisé
    if (
      typeof value.toString === 'function' &&
      value.toString !== Object.prototype.toString
    ) {
      const str = value.toString()
      if (str !== '[object Object]') return str
    }

    // Fallback au JSON stringify
    return JSON.stringify(value)
  }

  return String(value)
}

const toStrictNumber = (value: OptionValue): number | null => {
  const normalizedLabel = getOptionLabel(value).trim().replace(',', '.')

  if (!/^-?\d+(\.\d+)?$/.test(normalizedLabel)) {
    return null
  }

  const numericValue = Number.parseFloat(normalizedLabel)
  return Number.isFinite(numericValue) ? numericValue : null
}

const normalizeSizeLabel = (value: OptionValue): string =>
  getOptionLabel(value).trim().toUpperCase().replace(/\s+/g, '')

const sizeRank = (value: OptionValue): number | null => {
  const normalized = normalizeSizeLabel(value)

  const explicitRanks: Record<string, number> = {
    XXS: 1,
    XS: 2,
    S: 3,
    M: 4,
    L: 5,
    '1XL': 6,
    XL: 6,
    '2XL': 7,
    XXL: 7,
    '3XL': 8,
    XXXL: 8,
    '4XL': 9,
    XXXXL: 9,
  }

  if (explicitRanks[normalized]) {
    return explicitRanks[normalized]
  }

  const minusPattern = normalized.match(/^(X+)S$/)
  if (minusPattern) {
    return Math.max(0, 3 - minusPattern[1].length)
  }

  const plusPattern = normalized.match(/^(X+)L$/)
  if (plusPattern) {
    return 5 + plusPattern[1].length
  }

  const numericPlusPattern = normalized.match(/^(\d+)XL$/)
  if (numericPlusPattern) {
    return 5 + Number.parseInt(numericPlusPattern[1], 10)
  }

  return null
}

const shouldSortAsNumber = (
  values: OptionValue[],
  optionType?: string,
): boolean => {
  // When type is explicitly NUMBER, bypass the minimum length guard and force numeric sort
  if (optionType?.toUpperCase() === 'NUMBER') {
    return values.every((value) => toStrictNumber(value) !== null)
  }

  if (values.length < 2) {
    return false
  }

  return values.every((value) => toStrictNumber(value) !== null)
}

const shouldSortAsSize = (values: OptionValue[]): boolean => {
  if (values.length < 2) {
    return false
  }

  return values.every((value) => sizeRank(value) !== null)
}

const createSorter = (
  rankFn: (value: OptionValue) => number | null,
) => (
  leftValue: OptionValue,
  rightValue: OptionValue,
): number => {
  const leftRank = rankFn(leftValue) ?? Number.POSITIVE_INFINITY
  const rightRank = rankFn(rightValue) ?? Number.POSITIVE_INFINITY

  const diff = leftRank - rightRank
  if (diff !== 0) {
    return diff
  }

  return getOptionLabel(leftValue).localeCompare(
    getOptionLabel(rightValue),
    'fr',
  )
}

export const sortOptionValues = (
  values: OptionValue[],
  optionType?: string,
): OptionValue[] => {
  if (shouldSortAsNumber(values, optionType)) {
    return [...values].sort(createSorter(toStrictNumber))
  }

  if (shouldSortAsSize(values)) {
    return [...values].sort(createSorter(sizeRank))
  }

  return values
}

/**
 * Parse une valeur stringifiée en objet OptionValue
 * Amélioré pour gérer les arrays et erreurs de parsing
 */
export const parseOptionValue = (value: string): OptionValue => {
  if (!value || typeof value !== 'string') return value

  // Détecter si c'est du JSON valide
  const trimmed = value.trim()
  const firstChar = trimmed[0]

  if (firstChar === '{' || firstChar === '[') {
    try {
      return JSON.parse(value)
    } catch {
      // Retourner la valeur originale si parsing échoue
    }
  }

  return value
}

/**
 * Compare deux valeurs (gère objets, tableaux, strings, null, undefined)
 * Fonction générique réutilisable pour toute comparaison de valeurs complexes
 */
export const areValuesEqual = <T>(value1: T, value2: T): boolean => {
  // Gestion des null/undefined
  if (value1 == null && value2 == null) return true
  if (value1 == null || value2 == null) return false

  // Si références identiques (même objet en mémoire)
  if (value1 === value2) return true

  // Comparaison d'objets/arrays avec JSON.stringify
  if (typeof value1 === 'object' && typeof value2 === 'object') {
    try {
      return JSON.stringify(value1) === JSON.stringify(value2)
    } catch {
      // En cas d'erreur (références circulaires), retourner false
      return false
    }
  }

  // Comparaison de valeurs primitives
  return String(value1) === String(value2)
}

/**
 * Convertit les options d'un variant en format pour selectedOptions (v-model)
 */
export const variantOptionsToSelectedOptions = (
  variantOptions: Record<string, OptionValue>,
): Record<string, string> => {
  const selectedOptions: Record<string, string> = {}

  Object.entries(variantOptions).forEach(([key, value]) => {
    selectedOptions[key] = getOptionValue(value)
  })

  return selectedOptions
}

export const areOptionsInitialized = (
  options: Record<string, string> | null | undefined,
): boolean => {
  return options != null && Object.keys(options).length > 0
}
