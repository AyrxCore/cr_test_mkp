import BaseClientService from '@/vuejs/services/BaseClientService'
import { BannerSearch } from '@/vuejs/types/BannerSearch'

export default class BannerSearchHttpClient extends BaseClientService {
  public getBannersSearch(): Promise<BannerSearch[]> {
    return this.apiClient.get('banners-search').then((response) => {
      return response.data
    })
  }
}
