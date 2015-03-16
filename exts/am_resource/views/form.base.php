<?php

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
