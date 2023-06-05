import DefaultClientService from '@/vuejs/services/DefaultClientService'

export default class CmsHttpClient extends DefaultClientService {
  public getPageById<T extends []>(id): Promise<T> {
    return this.client
      .get<T>(`cms/page/${id}`)
      .then((response) => response.data)
  }
}
