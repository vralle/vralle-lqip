'use strict';

import path from 'path';
import babel from 'rollup-plugin-babel';
import resolve from 'rollup-plugin-node-resolve';

const pkg = require( path.resolve( './package.json' ) );
const year = new Date().getFullYear();

module.exports = [
  {
    input: path.resolve( './src/lqip.js' ),
    output: {
      file: path.resolve( './dist/lqip.js' ),
      format: 'iife',
      globals: {
        window: 'window',
        document: 'document',
      },
      banner: `/*!
  * LQIP for vralle-lazyload v${pkg.version}
  * Copyright ${year} V.Ralle
  * Licensed under MIT
  */`
    },
    external: [ 'window', 'document' ],
    plugins: [
      resolve(),
      babel( {
        exclude: '/node_modules\/(?!stackblur-canvas)/',
      } ),
    ],
  },
];
