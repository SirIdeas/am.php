(:: parent:views/base.php :)

<section class="site-body">
  <div class="content row">
    <div class="site-sidebar col col3">
      (:: place:views/sidebar.php :)
    </div>
    <div class="col os-s3 s9">
      <div class="inner">(:: child :)</div>
    </div>
  </div>
</section>