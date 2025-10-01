import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                // Brand: blue/purple with neon accent
                primary: {
                    50: '#eef2ff',
                    100: '#e0e7ff',
                    200: '#c7d2fe',
                    300: '#a5b4fc',
                    400: '#818cf8',
                    500: '#6366f1', // indigo-500
                    600: '#4f46e5',
                    700: '#4338ca',
                    800: '#3730a3',
                    900: '#312e81',
                },
                secondary: {
                    50: '#fdf4ff',
                    100: '#fae8ff',
                    200: '#f5d0fe',
                    300: '#f0abfc',
                    400: '#e879f9',
                    500: '#d946ef', // fuchsia-500
                    600: '#c026d3',
                    700: '#a21caf',
                    800: '#86198f',
                    900: '#701a75',
                },
                accent: {
                    400: '#22d3ee', // neon-cyan 400
                    500: '#06b6d4', // cyan-500
                    600: '#0891b2',
                },
                neutral: {
                    50: '#fafafa',
                    100: '#f5f5f5',
                    200: '#e5e5e5',
                    300: '#d4d4d4',
                    400: '#a3a3a3',
                    500: '#737373',
                    600: '#525252',
                    700: '#404040',
                    800: '#262626',
                    900: '#171717',
                },
                success: { 500: '#22c55e', 600: '#16a34a' },
                warning: { 500: '#f59e0b', 600: '#d97706' },
                danger: { 500: '#ef4444', 600: '#dc2626' },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            container: {
                center: true,
                padding: {
                    DEFAULT: '1rem',
                    md: '1.5rem',
                    lg: '2rem',
                    xl: '2.5rem',
                },
            },
            boxShadow: {
                soft: '0 2px 10px rgba(0,0,0,0.06)',
                elevated: '0 10px 25px rgba(0,0,0,0.10)',
                neon: '0 0 0 3px rgba(34,211,238,0.3)',
            },
            borderRadius: {
                xl: '1rem',
                '2xl': '1.25rem',
            },
        },
    },
    plugins: [forms],
};
