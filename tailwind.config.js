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
            fontFamily: {
                // Mengganti Figtree dengan Inter sebagai font utama
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            // Menambahkan warna kustom (opsional, tapi bagus untuk konsistensi)
            colors: {
                slate: {
                    850: '#1e293b', // Warna khusus untuk header
                }
            }
        },
    },

    plugins: [forms],
};