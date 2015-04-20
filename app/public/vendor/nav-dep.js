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
 * Este archivo carga un dialog de BS con un mensaje para los navegadores descontinuados
 * Necesita Boostrap
 */

(function($){
  'use strict';

  // Variable que indica que esta libreria esta cargada.
  // Se puede asignar una funcion a esta variable para realizar una accion cuando se cierre el dialogo
  window.__waitNavDepModal = true;

  var view = [
    '<div class="modal fade">',
      '<div style="display: table; width: 100%; height: 100%;">',
        '<div style="display: table-cell;vertical-align:middle;">',
          '<div class="modal-content" style="width: 500px; margin: 0 auto;">',
            '<div class="modal-body">',
              '<p>',
                'Su navegador está <strong>descontinuado</strong>, por motivos de <strong>seguridad</strong>',
                'y para disfrutar de una experiencia completa en la aplicación, es recomendable actualizarlo.',
              '</p>',
              '<p>',
                'Por favor <a a-there href="http://browsehappy.com/" target="_blank">actualice su navegador</a> para',
                'mejorar su experiencia en la web, o haga clic en el botón para cerrar éste diálogo.',
              '</p>',
            '</div>',
            '<div class="modal-footer">',
              '<a a-close type="button" class="btn btn-danger">',
                'Continuar',
              '</a>',
            '</div>',
          '</div>',
        '</div>',
      '</div>',
    '</div>',
  ].join('');

  $(function(){

    var modal = $(view);      // convertir a jQuery
    $('body').append(modal);  // Agregar al cuerpo

    modal.find('a[a-close]').click(function(){
      modal.modal('hide');
    });

    // Cuando ce cierre la ventana se llamara la
    // window.__waitNavDepModal si esta es una funcion
    modal.on('hidden.bs.modal', function(){
      if(typeof(window.__waitNavDepModal)==="function"){
        window.__waitNavDepModal();
      }
    });

    // Mostrar modal
    modal.modal('show');

  });

})($);
