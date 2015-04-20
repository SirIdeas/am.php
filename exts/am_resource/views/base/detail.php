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

  <div>
    <h3>Detalle <strong><?php echo $classModel ?></strong></h3>
    <?php if ($allow["edit"]): ?>
      <a href="edit" class="boton-inline">[Editar]</a>
    <?php endif ?>
    <?php if ($allow["delete"]): ?>
      <a href="delete" class="boton-inline">[Eliminar]</a>
    <?php endif ?>
    <a href="<?php echo $url ?>" class="boton-inline">[Volver]</a>
  </div>

  <?php foreach ($fields as $fieldName): ?>
    <div class="form-group">
      <label><?php echo $fieldName ?></label>
      <div class="field-readonly">
        <?php echo $r->$fieldName ?>
      </div>
    </div>
  <?php endforeach ?>

</div>
