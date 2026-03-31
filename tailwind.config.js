/** @type {import('tailwindcss').Config} */
const baColors = require( './resources/css/baColors.json' );
export default {
    prefix: 'ba-',
    content: [
        './resources/**/*.{js,css}',
        './src/**/*.php',
        './templates/*.php',
        './templates/**/*.php',
        './views/**/*.php',
    ],
    theme: {
        colors: {
            ...baColors,
        },
        extend: {},
    },
    plugins: [],
};
