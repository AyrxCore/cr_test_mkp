import DefaultClientService from '@/vuejs/services/DefaultClientService'
import type { NewsResponse, News } from '@/vuejs/types/News'

export type { NewsResponse, News }

export default class NewsHttpClient extends DefaultClientService {
  public getNews(): Promise<NewsResponse> {
    return this.client
      .get<NewsResponse>('news')
      .then((response) => response.data)
  }
}
