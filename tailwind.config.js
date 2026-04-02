const plugin = require( 'tailwindcss/plugin' );

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
            'black': '#000',
            'white': '#fff',
            ...baColors,
        },
        extend: {},
    },
    plugins: [
        plugin( ( {matchUtilities} ) => {
            matchUtilities( {
                'x': ( value ) => ( {
                [`@apply ${value.replaceAll( ',', ' ' )}`]: {},
                } ),
            } );
            } ),
    ],
};
