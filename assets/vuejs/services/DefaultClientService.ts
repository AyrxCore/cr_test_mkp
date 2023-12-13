import axios, {
  AxiosError,
  AxiosInstance,
  AxiosRequestConfig,
  AxiosResponse,
} from 'axios'

import store from '@/vuejs/store'
import { useCommonStore } from '@/vuejs/stores/common'

const commonStore = useCommonStore(store)

class DefaultClientService {
  public client: AxiosInstance
  public static self: InstanceType<any>

  public constructor() {
    const headers = {
      accept: 'application/json',
      'Content-Type': 'application/json',
    }

    this.client = axios.create({
      baseURL: '/api',
      withCredentials: true,
      headers,
    })

    this.client.interceptors.request.use(
      (config: AxiosRequestConfig): AxiosRequestConfig => {
        if (!config.url.includes('/channels/by-host/')) {
          config.headers['X-channel'] = commonStore.channelCode
        }

        return config
      },
      (error: AxiosError): Promise<AxiosError> => {
        return Promise.reject(error)
      },
    )

    this.client.interceptors.response.use(
      (response: Promise<AxiosResponse> | AxiosResponse | undefined) => {
        return response
      },
      async (error: AxiosError) => {
        return Promise.reject(error)
      },
    )
  }

  public static get<T extends typeof DefaultClientService>(
    this: T,
  ): InstanceType<T> {
    const instance = new this()
    this.self = instance
    return instance as InstanceType<T>
  }
}

export default DefaultClientService
