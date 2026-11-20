import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                cream: '#FFF7FA',
                blush: '#E8A9BC',
                petal: '#F6D8E1',
                rose: '#EFC4D2',
                ink: '#3B3336',
                muted: '#8C7D82',
                line: '#F0E1E5',
            },
        },
    },

    plugins: [forms],
};
