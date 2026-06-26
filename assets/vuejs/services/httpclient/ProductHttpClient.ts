import BaseClientService from '@/vuejs/services/BaseClientService'
import { Product, ProductCollection } from '@/vuejs/types/Product'

export default class ProductHttpClient extends BaseClientService {
  public fetchProductsByParams(params): Promise<ProductCollection> {
    const queryString = Object.keys(params)
      .map((key) => {
        if (Array.isArray(params[key])) {
          return params[key]
            .map(
              (value) =>
                `${encodeURIComponent(key)}[]=${encodeURIComponent(value)}`,
            )
            .join('&')
        } else if (typeof params[key] === 'object') {
          return `${encodeURIComponent(key)}=${encodeURIComponent(
            JSON.stringify(params[key]),
          )}`
        } else {
          // Si la valeur n'est pas un objet, l'inclure telle quelle
          return `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`
        }
      })
      .join('&')
    return this.apiClient
      .get<ProductCollection>(`products?${queryString}`)
      .then((response) => response.data)
  }

  public findProductById(id: number | string): Promise<Product> {
    return this.apiClient
      .get(`products/${id}`)
      .then((response) => response.data)
  }

  public findAccordCadreById(id: number | string): Promise<Product> {
    return this.apiClient
      .get(`products/${id}`)
      .then((response) => response.data)
  }

  public updateAccountAccordsCadresByParams<T>(params): Promise<T> {
    return this.apiClient
      .post<T>('accord-cadre-subscription', params)
      .then((response) => response.data)
  }

  public sendContactRequestFromNotSellableProduct<T>(data: object): Promise<T> {
    return this.apiClient
      .post<T>('not-sellable-contact-request', data)
      .then((response) => response.data)
  }

  public updateStellantisSubscription<T>(): Promise<T> {
    return this.apiClient
      .post<T>('stellantis-subscription')
      .then((response) => response.data)
  }

  public cancelStellantisSubscription<T>(): Promise<T> {
    return this.apiClient
      .post<T>('cancel-stellantis-subscription')
      .then((response) => response.data)
  }

  public downloadPdfFile<T>(url: string): Promise<T> {
    return this.apiClient
      .get(`edit-download-pdf-file?url=${url}`)
      .then((response) => response.data)
  }
}
