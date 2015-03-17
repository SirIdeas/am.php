<?php
  $msgs = array(
    "success" => "success",
    "warning" => "warning",
    "danger" => "danger",
    "error" => "danger"
  );
?>

<?php foreach ($msgs as $key => $class):?>
  <?php $group = AmFlash::get($key); if(empty($group)) continue; ?>
  <div class="alert alert-<?php echo $class ?>" role="alert">
    <?php foreach ($group as $msg): ?>
      <div><?php echo $msg ?></div>
    <?php endforeach ?>
  </div>
<?php endforeach ?>
