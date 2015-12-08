// De esto
'/' => 'views => views/index.php',
'action1' => 'myAdmin => act1',
array(
  'route' => '/action2',
  'myAdmin' => 'act2'
),


// a esto
'/' => 'views => views/index.php', // Ignored
array(
  'route' => '/admin/action1',
  'call' => 'Admin::act1'
),
array(
  'route' => '/admin/action2',
  'call' => 'Admin::act3'
),
