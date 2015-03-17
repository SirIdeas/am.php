<?php if (!empty($googleAnalitics)): ?>
  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    ga('create', '<?php echo $googleAnalitics ?>', 'auto');
    ga('send', 'pageview');
  </script>
<?php endif ?>

<!--[if lt IE 9]>
  <script src="<?php Am::eUrl() ?>/vendor/ie-fixs.js"></script>
<![endif]-->

<script src="<?php Am::eUrl() ?>/vendor/vendor.js"></script>

<!--[if lt IE 9]>
  <script src="<?php Am::eUrl() ?>/vendor/nav-dep.js"></script>
<![endif]-->

<script src="<?php Am::eUrl() ?>/vendor/cookie-msg.js"></script>
<script type="text/javascript">
  $.cookieMsg('bottom', 'simposiumtos-web', '<?php Am::eUrl() ?>/politicas-de-privacidad');
</script>
