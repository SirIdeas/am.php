(: section:'modelhead'
  <h2>(:= $form :)</h2>
(: endSection

(: section:'modellabel'
  <label for="(:= $form.'_'.$fieldname :)">
    (:= $field['label'] :)
  </label>
(: endSection

(: section:'modelinput'
  <input
    id="(:= $form.'_'.$fieldname :)"
    type="(:= $field['type'] :)"
    name="(:= $form.'['.$fieldname.']' :)"
    value="(:= $r->$fieldname :)"
    (:= $field['required'] ? 'required' : '' :)
    (:= isset($field['len']) ? 'maxlength="'.$field['len'].'"' : '' :) />
(: endSection

(: section:'modelfield'
  <div>
    (: put:'modellabel'
    (: put:'modelinput'
  </div>
(: endSection

(: section:'modelbody'
  (: foreach($forms[$formName] as $fieldname => $field):
    (: put:'modelfield'
  (: endforeach
(: endSection

(: section:'modelsubmit'
  <button type="submit">Enviar</button>
(: endSection

(: section:'modelfooter'
  (: put:'modelsubmit'
  <button type="reset">Resetear</button>
(: endSection

(: section:'modelform'
  <form method="post" name="(:= $form :)" >
    (: put:'modelhead'
    (: put:'modelbody'
    (: put:'modelfooter'
  </form>
(: endSection
