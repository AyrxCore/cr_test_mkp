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

export function formatPrice(price: number): string {
  return price.toLocaleString('fr', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })
}

export function formatAddress(address: Address): string {
  if (!address) return null
  return `${!address.company ? '' : address.company + ', '} ${address.street} ${
    address.postcode
  } ${address.city}`
}
