(: parent:'_tpl.php'
(: $formName = 'detail'
(: insert:'_form.php'

(: section:'form-head'
  <h2>Detail (:= $form :)</h2>
(: endSection

(: deleteSection:'form-input'

(: section:'form-label'
  <label for="(:= $form.'_'.$fieldname :)">
    (:= $field['label'] :): <strong>(:= $r->$fieldname :)</strong>
  </label>
(: endSection

(: section:'form'
  (: put:'flash-messages'
  <div>
    (: put:'form-head'
    (: put:'form-body'
  </div>
(: endSection

(: put:'form'