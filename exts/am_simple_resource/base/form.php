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
(# set:formTitle='' #)

(# section:resourceErrors #)
  <?php if(count($resourceErrors = $r->getErrors())): ?>
    <div class="row">
      <div class="card-panel deep-orange lighten-3 deep-orange-text text-darken-4">
        <?php foreach ($resourceErrors as $field => $msgs): ?>
          <strong><?php echo itemOr($field, $fieldNames, $field) ?>:</strong><br>
          <?php foreach ($msgs as $validator => $msg): ?>
            &nbsp;<small><?php echo "- {$msg}" ?></small><br>
          <?php endforeach ?>
        <?php endforeach ?>
      </div>
    </div>
  <?php endif ?>
(# endsection #)

<?php
  $resourceForm = new HTMLForm(array_merge_recursive(
    array(
      'record' => $r,
      'hides' => $hides,
      'head' => HTML::t('div', array(
        HTML::t('h3', $formTitle),
        HTML::t('a', '[Volver]', array('href'=>$url))
      )),
      'attrs' => array(
        'name' => $classModel
      ),
    ),
    $formConf
  )
);

?>

(# section:resourceForm #)
  (# put:resourceErrors #)
  <?php echo $resourceForm; ?>
(# endsection #)
