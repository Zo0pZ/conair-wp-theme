/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.html',
    './parts/**/*.html',
    './patterns/**/*.php',
    './inc/**/*.php',
    './functions.php',
  ],
  theme: {
    extend: {
      colors: {
        ink: {
          DEFAULT: '#0c0c0c',
          900: '#0c0c0c',
          800: '#141414',
          700: '#1c1c1c',
          600: '#242424',
        },
        teal: {
          DEFAULT: '#00b4a2',
          light: '#00ccb8',
          dark: '#009688',
        },
      },
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      minHeight: { tap: '44px' },
      minWidth: { tap: '44px' },
    },
  },
  plugins: [],
  corePlugins: {
    preflight: false,
  },
};
