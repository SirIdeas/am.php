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

/**
 * Este archivo carga mensaja de alerta sobre el suo de cookies
 * Necesita Jquery
 */

(function($){
  'use strict';

  $.cookieMsg = function(pos, key, aboutHref){

    if(localStorage[key]!=='true'){

      var view = [
        '<div style="position: fixed;'+pos+': 0;left: 0;right: 0;background-color: #000;z-index: 9999;color: #fff;text-align: center;padding: 10px 30px;opacity: 0.8;-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=80)";filter: alpha(opacity=80);">',
          '<div style="position:absolute; right:0; top:0; bottom:0;">',
            '<div style="display:table; height:100%;">',
              '<div style="display:table-cell; vertical-align:middle; padding-right:10px;">',
                '<a a-close style="font-size:18px; float:right; color:#fff; text-decoration:none; cursor:pointer;" class="glyphicon glyphicon-remove" alt="Cerrar"></a>',
              '</div>',
            '</div>',
          '</div>',
          '<p>',
            'Utilizamos cookies propias y de terceros para realizar análisis de uso y de medición',
            'de nuestra webpara mejorar nuestros servicios.<br>Si continua navegando, consideramos',
            'que acepta su uso. Puede cambiar la configuración u obtener más información ',
            '<a a-there href="#">aquí</a>.',
          '</p>',
        '</div>'
      ].join('');

      $(function(){

        var modal = $(view);      // convertir a jQuery
        $('body').append(modal);  // Agregar al cuerpo

        modal.find('a[a-close]').click(function(){
          modal.fadeOut();
          localStorage[key]='true';
        });

        // Asignar enlace de acerca de
        modal.find('a[a-there]').attr('href', aboutHref);

        // Mostrar modal
        modal.fadeIn();

      });

    }

  };

})($);
