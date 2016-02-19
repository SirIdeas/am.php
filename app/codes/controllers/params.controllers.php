// /app/controllers.conf.php
<?php
return array(
  'Foo' => array(
    'filters' => array(
      'before' => array(
        'baz' => 'all'
      ),
      'after' => array(
        'qux' => 'all'
      )
    )
  )
);