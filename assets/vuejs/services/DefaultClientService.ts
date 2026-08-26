import axios, {
  AxiosError,
  AxiosInstance,
  AxiosResponse,
  InternalAxiosRequestConfig,
} from 'axios'

import { useCommonStore } from '@/vuejs/stores/common'
import { channelReadyPromise } from '@/vuejs/services/channelReadyPromise'

// Initialisation différée pour éviter l'erreur "no active Pinia"
let commonStore: ReturnType<typeof useCommonStore> | null = null

const getCommonStore = () => {
  if (!commonStore) {
    commonStore = useCommonStore()
  }
  return commonStore
}

class DefaultClientService {
  public client: AxiosInstance
  public static self: DefaultClientService

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
      async (config: InternalAxiosRequestConfig): Promise<InternalAxiosRequestConfig> => {
        if (!config.url.includes('/channels/by-host/')) {
          await channelReadyPromise
          const store = getCommonStore()
          if (!store.channelCode) {
            return Promise.reject(new Error('Channel failed to initialize — request aborted'))
          }
          config.headers['X-channel'] = store.channelCode
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
