/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                floresta: '#00523D',
                pastel: '#D6F2C2',
                limao: '#8DE55D',
                lima: '#D3F452',
                intenso: '#038E39',
                ameixa: '#2E1E3D',
            },
            fontFamily: {
                titulo: ['"Baloo 2"', 'system-ui', 'sans-serif'],
                subtitulo: ['Montserrat', 'system-ui', 'sans-serif'],
                texto: ['"Nunito Sans"', 'system-ui', 'sans-serif'],
            },
            gridTemplateColumns: {
                'principal': 'minmax(0, 55fr) minmax(0, 45fr)',
            },
        },
    },
    plugins: [],
};
