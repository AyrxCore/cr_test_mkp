export const PASSWORD_RULES = [
  {
    label: 'Doit contenir au moins 12 caractères.',
    test: (p: string) => p.length >= 12,
  },
  {
    label: 'Au moins une majuscule (A-Z).',
    test: (p: string) => /[A-Z]/.test(p),
  },
  {
    label: 'Au moins une minuscule (a-z).',
    test: (p: string) => /[a-z]/.test(p),
  },
  {
    label: 'Au moins un chiffre (0-9).',
    test: (p: string) => /[0-9]/.test(p),
  },
  {
    label: 'Au moins un caractère spécial (ex: !@#$%^&_).',
    test: (p: string) => /[^A-Za-z0-9]/.test(p),
  },
]

export const isPasswordValid = (password: string): boolean =>
  PASSWORD_RULES.every((rule) => rule.test(password))
