import { notify } from 'notiwind'

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

export function getUrlParam(name: string): string | null {
  let params = new URLSearchParams(document.location.search)
  return params.get(name)
}

export function notifyError(text: string, time: number = 10000): void {
  notify(
    {
      group: 'notif',
      type: 'error',
      title: 'Une erreur est survenue',
      text: text,
    },
    time,
  )
}

export function notifySuccess(text: string): void {
  notify(
    {
      group: 'notif',
      type: 'success',
      title: 'Succès!',
      text: text,
    },
    5000,
  )
}
