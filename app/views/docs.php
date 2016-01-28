(:: parent:views/base.php :)
(:: set:pageTitle='Amathista Framework' :)

<div id="pageTitle" class="page-title-container primary bg-d1 box-shadow-2">
  <div class="content row spyscroll dispel" data-not-equal="0" data-class="on">
    <div class="col s3">
      <img src="(:/:)/images/am_vlogo_w.png" alt="" class="sub-header-logo">
      &nbsp;
    </div>
    <div class="col s9 title-parent">
      <h1>(:= $pageTitle :)</h1>
    </div>
  </div>
</div>

<div class="content row">
  <div class="col s9 ofs3 inner">
    (:: child :)
  </div>
</div>