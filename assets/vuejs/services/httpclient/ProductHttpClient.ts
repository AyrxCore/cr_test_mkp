import BaseClientService from '@/vuejs/services/BaseClientService'
import { Product, ProductCollection } from '@/vuejs/types/Product'

export default class ProductHttpClient extends BaseClientService {
  public fetchProductsByParams<T extends []>(
    params,
  ): Promise<ProductCollection> {
    const queryString = Object.keys(params)
      .map((key) => {
        if (typeof params[key] === 'object') {
          // Si la valeur est un objet, la sérialiser en JSON
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
      .get<T>(`products?${queryString}`)
      .then((response) => response.data[0])
  }

  public findProductById<T extends []>(id: number): Promise<Product> {
    return this.apiClient
      .get(`products/${id}`)
      .then((response) => response.data)
  }

  public findVariantById<T extends []>(id: number): Promise<Product> {
    return this.apiClient.get(`variant/${id}`).then((response) => response.data)
  }

  public findAccordCadreById<T extends []>(id: number): Promise<Product> {
    return this.apiClient
      .get(`products/${id}`)
      .then((response) => response.data)
  }

  public updateAccountAccordsCadresByParams<T extends []>(params): Promise<T> {
    return this.apiClient
      .post<T>('accord-cadre-subscription', params)
      .then((response) => response.data)
  }

  public sendContactRequestFromNotSellableProduct<T extends []>(
    data: Object,
  ): Promise<T> {
    this.apiClient.defaults.headers['Content-Type'] = 'multipart/form-data'
    return this.apiClient
      .post<T>('not-sellable-contact-request', data)
      .then((response) => response.data)
  }

  public updateStellantisSubscription<T extends []>(): Promise<T> {
    return this.apiClient
      .post<T>('stellantis-subscription')
      .then((response) => response.data)
  }

  public downloadPdfFile<T extends []>(url: string): Promise<T> {
    return this.apiClient
      .get(`edit-download-pdf-file?url=${url}`)
      .then((response) => response.data)
  }
}
