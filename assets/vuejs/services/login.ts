import { LoginResponse } from '@/vuejs/types/User'

export function getErrorMessage(errorCode: string): string {
  let message = ''
  switch (errorCode) {
    case LoginResponse.UserDisabled:
      message =
        "Votre compte n'est pas actif. Pour l'activer, cliquez sur le bouton “activer mon compte” et suivez les instructions."
      break
    case LoginResponse.UserEmptyAccount:
      message =
        'Aucun compte ne correspond à cet email sur notre plateforme, veuillez contacter le service client'
      break
    case LoginResponse.InvalidCredentials:
      message =
        'Votre adresse email et/ou votre mot de passe semblent incorrects - veuillez réessayer'
      break
    default:
      message = 'Une erreur est survenue.'
      break
  }
  return message
}
