(: section:'formhead'
  <h2>(:= $formTitle :)</h2>
(: endSection

(: section:'formlabel'
  <label for="(:= $form.'_'.$fieldname :)">
    (:= $field['label'] :)
  </label>
(: endSection

(: section:'forminput'
  <input
    id="(:= $form.'_'.$fieldname :)"
    type="(:= $field['type'] :)"
    name="(:= $form.'['.$fieldname.']' :)"
    (:= $field['required'] ? 'required' : '' :)
    (:= isset($field['len']) ? 'maxlength="'.$field['len'].'"' : '' :) />
(: endSection

(: section:'formfield'
  <div>
    (: put:'formlabel'
    (: put:'forminput'
  </div>
(: endSection

(: section:'formbody'
  (: foreach($fields as $fieldname => $field):
    (: put:'formfield'
  (: endforeach
(: endSection

(: section:'formsubmit'
  <button type="submit">Enviar</button>
(: endSection

(: section:'formfooter'
  (: put:'formsubmit'
  <button type="reset">Resetear</button>
(: endSection

(: section:'formform'
  <form method="post" name="(:= $form :)" >
    (: put:'formhead'
    (: put:'formbody'
    (: put:'formfooter'
  </form>
(: endSection