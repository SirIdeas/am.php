(: insert:'_flash.php'

(: section:'form-head'
  <h2>(:= $formTitle :)</h2>
(: endSection

(: section:'form-label'
  <label for="(:= $formName.'_'.$fieldname :)">
    (:= $field['label'] :)
  </label>
(: endSection

(: section:'form-input'
  <input
    id="(:= $formName.'_'.$fieldname :)"
    type="(:= $field['type'] :)"
    name="(:= $formName.'['.$fieldname.']' :)"
    (:= $field['required'] ? 'required' : '' :)
    (:= isset($field['len']) ? 'maxlength="'.$field['len'].'"' : '' :) />
(: endSection

(: section:'form-field'
  <div>
    (: put:'form-label'
    (: put:'form-input'
  </div>
(: endSection

(: section:'form-body'
  (: foreach($fields as $fieldname => $field):
    (: put:'form-field'
  (: endforeach
(: endSection

(: section:'form-submit'
  <button type="submit">Enviar</button>
(: endSection

(: section:'form-footer'
  (: put:'form-submit'
  <button type="reset">Resetear</button>
(: endSection

(: section:'form'
  (: put:'flash-messages'
  <form method="post" name="(:= $formName :)" >
    (: put:'form-head'
    (: put:'form-body'
    (: put:'form-footer'
  </form>
  (: put:'form-links'
(: endSection

(: insert:'form-post.php'