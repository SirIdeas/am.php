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
 
function input_field($field, $attrs, $r){ ?>
  <div class="form-group <?php if($r->getErrors($field)) echo "with-error"; ?>">
    <label for="input-<?php echo $field ?>">
      <small><?php echo $attrs["label"] ?></small>
    </label>
    <input id="input-<?php echo $field ?>" value="<?php echo $r->$field ?>" class="form-control" name="<?php echo $field ?>" type="<?php echo $attrs["type"] ?>" <?php echo isset($attrs["len"])?'maxlength="'.$attrs["len"].'"':''; ?> <?php echo $attrs["required"]? "required" : "" ?>>
  </div><?php
}

function input_checkbox($field, $attrs, $r){ ?>
  <div class="checkbox <?php if($r->getErrors($field)) echo "with-error"; ?>">
    <label>
      <input id="input-<?php echo $field ?>" type="checkbox" name="<?php echo $field ?>" <?php echo $r->$field? "checked" : "" ?>> <?php echo $attrs["label"] ?>
    </label>
  </div><?php
}

function input_readonly($field, $attrs, $r){ ?>
  <div class="form-group <?php if($r->getErrors($field)) echo "with-error"; ?>">
    <label><small><?php echo $attrs["label"] ?></small></label>
    <div class="field-readonly">
      <strong><?php echo $r->$field ?></strong>
    </div>
  </div><?php
}

?>


<?php if(count($errors = $r->getErrors())): ?>
  <div class="row">
    <div class="alert alert-danger" role="alert">
      <?php foreach ($errors as $field => $msgs): ?>
        <strong><?php echo itemOr($field, $fieldNames, $field) ?>:</strong><br>
        <?php foreach ($msgs as $validator => $msg): ?>
          &nbsp;<small><?php echo "- {$msg}" ?></small><br>
        <?php endforeach ?>
      <?php endforeach ?>
    </div>
  </div>
<?php endif ?>

<form role="form" class="formulario" method="post">

  <div class="row">
    <div class="col-md-12 text-right">
      <button class="boton-inline small" type="submit">GUARDAR</button>
      <a href="list" class="boton-inline small">VOLVER</a>
    </div>
  </div>

  <?php foreach ($fields as $fieldGroupName => $fieldGroup): ?>
    <h2><?php echo $fieldGroupName ?></h2>
    <div class="row">
      <?php foreach ($fieldGroup as $field => $attrs): ?>
        <div class="col-sm-<?php echo $attrs["cols"] ?>">
          <?php
          switch ($attrs["type"]) {
            case 'checkbox':
              input_checkbox($field, $attrs, $r);
              break;
            case 'readonly':
              input_readonly($field, $attrs, $r);
              break;
            default:
              input_field($field, $attrs, $r);
              break;
          }
          ?>
        </div>
      <?php endforeach ?>
    </div>
    <hr>
  <?php endforeach ?>
  <br>
  (# put:postEdit #)

</form>
