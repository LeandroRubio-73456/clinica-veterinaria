import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                base: '#F6F8F9',
                surface: '#FFFFFF',
                ink: '#12242B',
                muted: '#5C7078',
                hairline: '#DCE4E6',
                teal: {
                    DEFAULT: '#0E7C86',
                    dark: '#0B5F67',
                    light: '#E4F1F1',
                },
                amber: {
                    DEFAULT: '#E2963A',
                    light: '#FBEEDD',
                },
                success: {
                    DEFAULT: '#2F9E64',
                    light: '#E4F5EB',
                },
                alert: {
                    DEFAULT: '#D14343',
                    light: '#FBE8E8',
                },
            },
            fontFamily: {
                display: ['"Space Grotesk"', 'sans-serif'],
                body: ['"IBM Plex Sans"', 'sans-serif'],
                mono: ['"IBM Plex Mono"', 'monospace'],
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
