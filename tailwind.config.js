const plugin = require('tailwindcss/plugin')
const defaultTheme = require('tailwindcss/defaultTheme')
const colors = require('tailwindcss/colors')

module.exports = {
  mode: 'jit',
  content: {
    enabled: process.env.NODE_ENV === 'production',
    content: [
      './assets/**/*.{vue,ts}',
      './src/**/*.{html,js}',
      './templates/**/*.html.twig',
      './node_modules/flowbite/**/*.js',
      './node_modules/flowbite-vue/**/*.{js,jsx,ts,tsx}',
    ],
  },
  theme: {
    extend: {
      // here's how to extend fonts if needed
      fontFamily: {
        sans: [...defaultTheme.fontFamily.sans],
      },
      colors: {
        primary: '#050056',
        secondary: '#9553ff',
        tertiary: '#0bb0fa',
        'gradient-1': '#404fe6',
        'gradient-2': '#00c7ff',
        // gray: '#5e6875',
      },
    },
  },
  plugins: [
    require('@tailwindcss/aspect-ratio'),
    require('@tailwindcss/line-clamp'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/forms'),
    require('flowbite/plugin'),
    plugin(function ({ addVariant, e, postcss }) {
      addVariant('firefox', ({ container, separator }) => {
        const isFirefoxRule = postcss.atRule({
          name: '-moz-document',
          params: 'url-prefix()',
        })
        isFirefoxRule.append(container.nodes)
        container.append(isFirefoxRule)
        isFirefoxRule.walkRules((rule) => {
          rule.selector = `.${e(
            `firefox${separator}${rule.selector.slice(1)}`,
          )}`
        })
      })
    }),
  ],
}
