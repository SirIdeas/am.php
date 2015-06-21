<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Sir Ideas, C. A.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 **/

$__DIR__ = dirname(__FILE__);

return array(
  // Rutas fisicas
  'concat' => array(
    // Target
    'public' => array(
      $__DIR__.'/public/vendor/vendor.css' => array(
        // Archivos fisicos
        $__DIR__.'/bower_components/materialize/dist/css/materialize.min.css'
      ),
      $__DIR__.'/public/vendor/ie-fixs.js' => array(
        $__DIR__.'/bower_components/es5-shim/es5-shim.min.js',
        $__DIR__.'/bower_components/json3/lib/json3.min.js'
      ),
      $__DIR__.'/public/vendor/vendor.js' => array(
        $__DIR__.'/bower_components/jquery/dist/jquery.min.js',
        $__DIR__.'/bower_components/materialize/dist/js/materialize.min.js'
      )
    )
  ),
  'copy' => array(
    'public' => array(
      array(
        'cwd' => $__DIR__.'/bower_components/materialize/',
        'dest' => $__DIR__.'/public/',
        'src' => array(
          'font/**/*'
        )
      )
    )
  )
);
