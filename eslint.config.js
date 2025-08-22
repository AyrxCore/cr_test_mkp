import js from '@eslint/js'
import typescript from '@typescript-eslint/eslint-plugin'
import typescriptParser from '@typescript-eslint/parser'
import vue from 'eslint-plugin-vue'
import vueParser from 'vue-eslint-parser'
import prettier from 'eslint-config-prettier'
import globals from 'globals'

export default [
  js.configs.recommended,

  {
    files: ['**/*.{js,ts,vue}'],
    languageOptions: {
      ecmaVersion: 2020,
      sourceType: 'module',
      globals: {
        ...globals.browser,
        ...globals.node,
        ...globals.es2022,
        defineProps: 'readonly',
        defineEmits: 'readonly',
        defineExpose: 'readonly',
        withDefaults: 'readonly',
      },
    },
    rules: {
      'eol-last': 'warn',
      'no-console': 'warn',
      'no-debugger': 'warn',
      'comma-dangle': ['error', 'only-multiline'],
      'no-useless-constructor': 'off',
      semi: ['warn', 'never'],
      quotes: ['warn', 'single', { avoidEscape: true }],
      'no-unused-vars': 'off',
    },
  },
  {
    files: ['**/*.{ts,vue}'],
    languageOptions: {
      parser: typescriptParser,
      parserOptions: {
        ecmaVersion: 2020,
        sourceType: 'module',
      },
    },
    plugins: {
      '@typescript-eslint': typescript,
    },
    rules: {
      ...typescript.configs.recommended.rules,
      '@typescript-eslint/no-unused-vars': [
        'error',
        { argsIgnorePattern: '^_' },
      ],
      '@typescript-eslint/explicit-function-return-type': 'off',
      '@typescript-eslint/explicit-module-boundary-types': 'off',
      '@typescript-eslint/no-explicit-any': 'warn',
    },
  },
  ...vue.configs['flat/essential'],
  ...vue.configs['flat/strongly-recommended'],
  {
    files: ['**/*.vue'],
    languageOptions: {
      parser: vueParser,
      parserOptions: {
        parser: typescriptParser,
        ecmaVersion: 2020,
        sourceType: 'module',
        extraFileExtensions: ['.vue'],
        project: './tsconfig.json',
      },
    },
    plugins: {
      '@typescript-eslint': typescript,
      vue: vue,
    },
    rules: {
      'vue/no-v-html': 'off',
      'vue/html-self-closing': [
        'error',
        {
          html: { void: 'always', normal: 'always', component: 'always' },
          svg: 'always',
          math: 'always',
        },
      ],
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
      'vue/multi-word-component-names': 'warn',
      'vue/no-mutating-props': 'error',
      'vue/require-v-for-key': 'error',
      'vue/no-deprecated-v-on-native-modifier': 'error',
    },
  },

  {
    ignores: [
      'app-api-schema.ts',
      'assets/modules/shared/components/icon/*.vue',
      'env.d.ts',
      'dist/',
      'node_modules/',
    ],
  },
  prettier,
]
