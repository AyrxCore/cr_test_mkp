import BaseClientService from '@/vuejs/services/BaseClientService'

import { Channel } from '@/vuejs/types/Channel'

export default class ChannelHttpClient extends BaseClientService {
  public getChannelByHost<T extends Channel>(hostname: string): Promise<T> {
    return this.apiClient
      .get<T>(`/channels/by-host/${hostname}`)
      .then((response) => response.data)
  }
}
