module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      borderWidth: {
        '1': '1px',
      },
      boxShadow: {
        'input': '0 0 1em 0 var(--tw-shadow-color)',
      },
      fontFamily: {
        'sans': ['kolektiv_grotesk'],
      },
      fontSize: {
        'sm': ['.875rem', {
          lineHeight: 1.2,
        }],
        'base': ['1rem', {
          lineHeight: 1.2,
        }],
        'lg': ['1.125rem', {
          lineHeight: 1.2,
        }],
        'lg': ['1.25rem', {
          lineHeight: 1.2,
        }],
      }
    },
  },
  plugins: [],
}
