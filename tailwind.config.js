const plugin = require( 'tailwindcss/plugin' );

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.{js,css}',
        './src/**/*.php',
        './templates/**/*.php',
        './views/**/*.php',
    ],
    theme: {
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
