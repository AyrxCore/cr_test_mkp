module.exports = {
  root: true,
  env: {
    node: true,
  },
  extends: [
    'eslint:recommended',
    'plugin:vue/vue3-essential',
    'plugin:vue/vue3-recommended',
    '@vue/standard',
    '@vue/typescript/recommended',
    'prettier',
  ],
  parserOptions: {
    ecmaVersion: 2020,
  },
  rules: {
    'eol-last': 'warn',
    'no-console': 'warn',
    'no-debugger': 'warn',
    'comma-dangle': ['error', 'only-multiline'],
    'no-useless-constructor': 'off',
    '@typescript-eslint/semi': ['warn', 'never'],
    'vue/no-v-html': 'off',
    'vue/html-self-closing': [
      'error',
      {
        html: {
          void: 'always',
          normal: 'always',
          component: 'always',
        },
        svg: 'always',
        math: 'always',
      },
    ],
    quotes: ['warn', 'single'],
    'vue/max-len': [
      'error',
      {
        code: 140,
        template: 140,
        tabWidth: 2,
        comments: 80,
        ignorePattern: '',
        ignoreComments: true,
        ignoreTrailingComments: false,
        ignoreUrls: false,
        ignoreStrings: true,
        ignoreTemplateLiterals: true,
        ignoreRegExpLiterals: false,
        ignoreHTMLAttributeValues: false,
        ignoreHTMLTextContents: true,
      },
    ],
  },
  globals: {
    defineProps: 'readonly',
    defineEmits: 'readonly',
    defineExpose: 'readonly',
    withDefaults: 'readonly',
  },
  ignorePatterns: [
    'app-api-schema.ts',
    'assets/modules/shared/components/icon/*.vue',
    'env.d.ts',
  ],
}
