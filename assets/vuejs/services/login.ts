import { LoginResponse } from '@/vuejs/types/User'

export function getErrorMessage(errorCode: string): string {
  let message = ''
  switch (errorCode) {
    case LoginResponse.UserDisabled:
      message =
        "Votre compte n'est pas actif, vous devez utiliser le lien de première connexion pour initialiser votre accès"
      break
    case LoginResponse.UserEmptyAccount:
      message =
        'Aucun compte ne correspond à cet email sur notre plateforme, veuillez contacter le service client'
      break
    case LoginResponse.InvalidCredentials:
      message = 'Identifiants incorrects'
      break
  }
  return message
}
