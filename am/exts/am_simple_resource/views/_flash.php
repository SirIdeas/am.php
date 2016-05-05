(: section:'flash-messages-item'
  <li>(:= $msg :)</li>
(: endSection

(: section:'flash-messages-group'
  <ul>
    (: foreach($group as $msg):
      (: put:'flash-messages-item'
    (: endforeach
  </ul>
(: endSection

(: section:'flash-messages'
  (: foreach($msgsKeys as $key):
    (: $group = AmFlash::get($key);
    (: if(!empty($group)):
      (: put:'flash-messages-group'
    (: endif
  (: endforeach
(: endSection
