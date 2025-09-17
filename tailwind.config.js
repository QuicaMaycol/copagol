import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                'azul': '#2A3A5B',
                'naranja': '#F97316',
                'rojo': '#E74C3C',
                'copa-blue': {
                    900: '#1E40AF',
                    700: '#3B82F6',
                    500: '#93C5FD'
                },
                'copa-red': '#EF4444',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};