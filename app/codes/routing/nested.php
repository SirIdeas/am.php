'/admin' => array(
  'call' => 'Admin::',
  'routes' => array(
    '/action1' => 'call => action1',
    array(
      'route' => '/action2',
      'call' => 'action2'
    )
  )
),

// Es lo mismo que

array(
  'route' => '/admin',
  'call' => 'Admin::',
  'routes' => array(
    '/action1' => 'call => action1',
    array(
      'route' => '/action2',
      'call' => 'action2'
    )
  )
),

// y es lo mismo que

'/admin/action1' => 'call => Admin::action1',
'/admin/action2' => 'call => Admin::action2',
