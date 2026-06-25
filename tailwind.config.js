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
                // Custom appetizing brand palette (overriding indigo for instant, safe global styling)
                // Theme: Terracotta & Warm Mustard (Estetik & Cozy)
                indigo: {
                    50: '#fffaf8',   // Lightest cream tint
                    100: '#fdf0e9',  // Warm cream
                    200: '#fbdad0',  // Soft terracotta pastel
                    300: '#f7bcae',  // Medium pastel terracotta
                    400: '#f1927d',  // Terracotta accent
                    500: '#e7654a',  // Primary Terracotta
                    600: '#d34e36',  // Button Terracotta
                    700: '#b23c27',  // Hover Terracotta
                    800: '#903120',  // Dark terracotta
                    900: '#742b1d',  // Deep terracotta
                    950: '#41130a',  // Darkest tone
                }
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};

