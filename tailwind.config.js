import forms from '@tailwindcss/forms';
import animate from 'tailwindcss-animate';
import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
  darkMode: ['class'],
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.tsx',
  ],

  theme: {
    extend: {
      colors: {
        cobalt: {
          50: '#eef8ff',
          100: '#d8eeff',
          200: '#bae1ff',
          300: '#8ad0ff',
          400: '#53b6ff',
          500: '#2b94ff',
          600: '#1475fc',
          700: '#0d5de8',
          800: '#1045ac',
          900: '#154293',
          950: '#122959',
        },
      },
      fontFamily: {
        sans: ['Radio Canada', ...defaultTheme.fontFamily.sans],
      },
      borderRadius: {
        lg: 'var(--radius)',
        md: 'calc(var(--radius) - 2px)',
        sm: 'calc(var(--radius) - 4px)',
      },
    },
  },

  plugins: [forms, animate],
};
