import resolve from 'rollup-plugin-node-resolve';
import babel from 'rollup-plugin-babel';
// import { terser } from 'rollup-plugin-terser';
import commonJs from 'rollup-plugin-commonjs';

export default {
    input: './client/session.js',
    output: {
        file: './dist/fmihel-ajax-plugin-session.js',
        format: 'cjs', // umd cjs iife
        name: 'fmihel_ajax_plugin_session',
    },
    external: ['jquery'],
    plugins: [

        resolve(),
        commonJs(),
        babel({
            exclude: 'node_modules/**', // only transpile our source code
        }),
        // terser(),
    ],

};
