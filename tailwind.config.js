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
    ],
  },
  theme: {
    extend: {
      // here's how to extend fonts if needed
      fontFamily: {
        sans: [...defaultTheme.fontFamily.sans],
      },
      fontSize: {
        'title-35': '35px',
      },
      spacing: {
        7.5: '1.875rem',
        8.5: '2.125rem',
      },
      colors: {
        primary: '#050056',
        secondary: '#9553ff',
        tertiary: '#0bb0fa',
        'gradient-1': '#404fe6',
        'gradient-2': '#00c7ff',
        // gray: '#5E6875',
        'green-qantis': '#65AC5D',
      },
    },
  },
  plugins: [
    require('@tailwindcss/aspect-ratio'),
    require('@tailwindcss/line-clamp'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/forms'),
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
