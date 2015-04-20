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
?>
<div>
  <h3>Listado <strong><?php echo $classModel ?></strong></h3>
  <?php if ($allow["new"]): ?>
    <a href="<?php echo $url ?>new" class="btn btn-default btn-xs">Nuevo</a>
    <br>
    <br>
  <?php endif ?>
</div>

<div>
  <input id="table-search" type="text" class="form-control" placeholder="Buscar">
</div>

<table
  class="table records-list"
  id="dinamic-table"
  data-param-len="15"
  data-param-data-url="<?php echo $url ?>data.json"
  data-param-input-search-selector="#table-search"
  data-param-pagination-selector="#table-pagination"
  data-param-count-record="#dinamic-table-count-record"
  data-param-field-row-state-class="cls"
>
  <thead>
    <tr>
      <?php foreach ($columns as $field => $def):
        $show = !isset($def["show"]) || $def["show"]==true;
        $sort = !isset($def["sort"]) || $def["sort"]==true;
        ?>
        <th data-param-show="<?php echo $show? "true" : "false" ?>">
          <?php if($show): ?>
            <span><?php echo itemOr($field, $fieldNames, $field) ?></span>
          <?php endif ?>
          <?php if($sort): ?>
            <span class="btn-xs" data-param-sort-asc>
              <span class="glyphicon glyphicon-chevron-down"></span>
            </span>
            <span class="btn-xs" data-param-sort-desc>
              <span class="glyphicon glyphicon-chevron-up"></span>
            </span>
          <?php endif ?>
        </th>
      <?php endforeach ?>

      <?php if ($allow["options"]): ?>
        <th data-param-show="false"></th>
        <th data-param-sort="false" data-param-class="text-center">
          <div>Opciones</div>
          <div class="with-200"></div>
        </th>
      <?php endif ?>

    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="<?php echo count($columns)+($allow["options"]? 1:0) ?>">
        <p id="dinamic-table-count-record" class="text-muted pull-left table-count-record">
          <i>
            <small dinamic-table-msg="showing">Mostrando registros del {$rf} - {$rt} de {$fc} encontrados de un total {$co} en la tabla</small>
            <small dinamic-table-msg="no-records-filtered">No se encontraron registros de un total {$co} en la tabla</small>
            <small dinamic-table-msg="no-records">No se existen registros en la tabla</small>
          </i>
        </p>
        <div class="pull-right">
          <ul id="table-pagination" class="pagination pagination-sm" data-param-pagination-items="2">
            <li><a data-param-page="first" href="#">&larr;</a></li>
            <li><a data-param-page="prev"  href="#">&laquo;</a></li>
            <li><a data-param-page="next"  href="#">&raquo;</a></li>
            <li><a data-param-page="last"  href="#">&rarr;</a></li>
          </ul>
        </div>
      </td>
    </tr>
  </tfoot>
</table>

<!-- The template to display files available for upload -->
<script id="template-options" type="text/x-tmpl">
  <small>
    <?php if ($allow["detail"]): ?>
      <a href="<?php $url ?>{%=o.id%}/detail">Detalle</a>&nbsp;|
    <?php endif ?>
    <?php if ($allow["edit"]): ?>
      <a href="<?php $url ?>{%=o.id%}/edit">Editar</a>&nbsp;|
    <?php endif ?>
    <?php if ($allow["delete"]): ?>
      <a href="<?php $url ?>{%=o.id%}/delete">Eliminar</a>
    <?php endif ?>
    (# put:recordOptions #)
  </small>
</script>

(# section:head+ #)
  <link rel="stylesheet" href="<?php Am::eUrl() ?>/exts/dinamictable/jquery.dinamictable.css">
(# endsection #)

(# section:foot+ #)
  <script src="<?php Am::eUrl() ?>/exts/dinamictable/tmpl.min.js"></script>
  <script src="<?php Am::eUrl() ?>/exts/dinamictable/jquery.dinamictable.js"></script>
  <script>
    $(function(){

      $('#dinamic-table').dinamictable({
        fnRecord: function(data, rowNumber, row){

          var result = [];

          for(var i in data){
            result.push(data[i]);
          }

          if($('#template-options').length>0){
            result.push(tmpl('template-options', data));
          }

          return result;
        }
      });

    });
  </script>
(# endsection #)
