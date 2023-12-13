import axios, {
  AxiosError,
  AxiosInstance,
  AxiosRequestConfig,
  AxiosResponse,
} from 'axios'
import store from '@/vuejs/store'
import { useCommonStore } from '@/vuejs/stores/common'

const commonStore = useCommonStore(store)

class BaseClientService {
  public apiClient: AxiosInstance
  public static self: InstanceType<any>

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

    this.apiClient.interceptors.response.use(
      (response: Promise<AxiosResponse> | AxiosResponse | undefined) => {
        return response
      },
      async (error: AxiosError) => {
        const originalConfig = error.config
        if (error.response?.status === 401 && !originalConfig._retry) {
          if (error.config.url !== 'authentication/token') {
            document.cookie = 'BEARER=; Max-Age=0'
            document.cookie = 'PHPSESSID=; Max-Age=0'
            location.reload()
          } else {
            console.log(error.config.url)
          }
        }
        if (error.response?.status === 503) {
          if (error.response?.data?.maintenance) {
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
    if (this.self && this.self.ispatch === isPatch) {
      return this.self
    }

    const instance = new this(isPatch)
    this.self = instance
    return instance as InstanceType<T>
  }
}

export default BaseClientService
