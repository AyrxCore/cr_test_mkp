import { Account } from '@/vuejs/types/Account'

// Décrit l'objet user
export interface User {
  email: string
  roles: string[]
  account: Account
  username: string
  firstName: string
  lastName: string
  editingInfo: string[]
  userInfoUpdateRequests: string[]
}

// Décrit le state général du store user
export interface UserStoreState {
  user: User
  editingInfo: string[]
}

// Décrit le bloc de données nécessaire à l'obtention du token
export interface AuthenticateUserData {
  username: string
  password: string
}

// décrit le bloc retourné par le back avec le token
export interface AuthenticateResponse {
  token: string
}

export enum LoginResponse {
  UserDisabled = 'user_disabled',
  UserEmptyAccount = 'user_empty_account',
  InvalidCredentials = 'Identifiants invalides.',
}

export interface PasswordChangeRequest {
  password: string
  confirmation: string
  currentPassword: string
}
