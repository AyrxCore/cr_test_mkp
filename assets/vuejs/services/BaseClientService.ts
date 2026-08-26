import axios, {
  AxiosError,
  AxiosInstance,
  AxiosResponse,
  InternalAxiosRequestConfig,
} from 'axios'
import Cookies from 'js-cookie'
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

class BaseClientService {
  public static self: BaseClientService
  public apiClient: AxiosInstance
  public isPatch: boolean

  public constructor(isPatch: boolean) {
    this.isPatch = isPatch
    const headers = {
      accept: 'application/json',
      'Content-Type': isPatch
        ? 'application/merge-patch+json'
        : 'application/json',
    }

    this.apiClient = axios.create({
      baseURL: '/api',
      withCredentials: true,
      headers,
    })

    this.apiClient.interceptors.request.use(
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

    this.apiClient.interceptors.response.use(
      (response: Promise<AxiosResponse> | AxiosResponse | undefined) => {
        return response
      },
      async (error: AxiosError) => {
        if (error.response?.status === 401) {
          if (error.config.url !== 'authentication/token') {
            Cookies.remove('BEARER')
            Cookies.remove('PHPSESSID')
            Cookies.remove('neoAutoLogin')
            location.reload()
          }
        }
        if (error.response?.status === 503) {
          if ((error.response?.data as { maintenance?: boolean } | null)?.maintenance) {
            location.reload()
          }
        }
        return Promise.reject(error)
      },
    )
  }

  public static get<T extends typeof BaseClientService>(
    this: T,
    isPatch = false,
  ): InstanceType<T> {
    if (this.self && this.self.isPatch === isPatch) {
      return this.self as InstanceType<T>
    }

    const instance = new this(isPatch) as InstanceType<T>
    this.self = instance
    return instance
  }
}

export default BaseClientService
