(:: parent:views/tpl.php :)
(:: set:pageTitle='Amathista Framework' :)

<header class="site-header primary bg-d1 spyscroll" data-height="#pageTitle" data-class="box-shadow-2">
  <div class="content row">
    <div class="inner col os-s2 col10 valign-middle spyscroll dispel on" data-equal="0" data-class="on">
      <div>(:= $pageTitle :)</div>
    </div>
  </div>
</header>

<section id="pageTitle" class="page-title primary bg-d1 box-shadow-2">
  <div class="content row">
    <div class="col col2 side">
      <div class="sub-header-logo white spyscroll" data-custom="true" style="height:100px"></div>
    </div>
    <div class="col col10 os-s2 title-parent spyscroll dispel" data-not-equal="0" data-class="on">
      <h1>(:= $pageTitle :)</h1>
    </div>
  </div>
</section>
(:: child :)
 
<footer class="site-footer secondary bg-l3">
  <div class="content row">
    <div class="col s8 valign-middle">
      <small>© 2014-2016 <a href="http://sirideas.com" target="_blank" class="link">Sir Ideas, C. A.</a>, Todos los derechos reservados.</small>
    </div>
    <div class="col s4 valign-middle text-right">
      <small>
        <a class="link" target="_blank" href="https://github.com/SirIdeas/amathista/blob/master/LICENSE">MIT License</a>, <a class="link" target="_blank" href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0.</a>
      </small>
    </div>
  </div>
</footer>