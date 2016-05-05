(: parent:'_tpl.php'
(: $formName = 'delete'
(: insert:'_form.php'

(: section:'form-head'
  <h2>Delete (:= $form :)</h2>
  <h3>Are you sure you want delete this record?</h3>
(: endSection

(: deleteSection:'form-input'

(: section:'form-label'
  <label for="(:= $form.'_'.$fieldname :)">
    (:= $field['label'] :): <strong>(:= $r->$fieldname :)</strong>
  </label>
(: endSection

(: section:'form-footer'
  <button type="submit">Yes</button>
(: endSection

(: put:'form'