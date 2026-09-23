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
                sans: [
                    '-apple-system',
                    'BlinkMacSystemFont',
                    '"SF Pro Text"',
                    '"SF Pro Display"',
                    'SFProText',
                    'SFProDisplay',
                    'system-ui',
                    ...defaultTheme.fontFamily.sans,
                ],
            },
            colors: {
                apple: {
                    blue: '#0071e3',
                    blueDark: '#0a84ff',
                    gray: '#86868b',
                    lightBg: '#f5f5f7',
                    darkBg: '#000000',
                    cardDark: '#1c1c1e',
                    secondaryDark: '#2c2c2e',
                    tertiaryDark: '#3a3a3c',
                }
            },
            boxShadow: {
                'apple-card': '0 2px 12px rgba(0, 0, 0, 0.04), 0 0 1px rgba(0, 0, 0, 0.08)',
                'apple-float': '0 12px 32px rgba(0, 0, 0, 0.1), 0 2px 6px rgba(0, 0, 0, 0.04)',
            }
        },
    },

    plugins: [forms],
};
