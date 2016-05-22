(: parent:'views/tpl.php'
(: $pageTitle='Amathista Framework'

<header class="site-header primary bg-d1 spyscroll" data-height="#pageTitle" data-class="box-shadow-2">
  <div class="content content-v row">
    <div class="inner side-bar-margin valign-middle spyscroll dispel2 on" data-equal="0" data-class="on">
      <div>(:= $pageTitle :)</div>
    </div>
  </div>
</header>

<section id="pageTitle" class="page-subtitle primary bg-d1 box-shadow-2">
  <div class="content content-v row">
    <!-- <div class="hide-s">
      <div class="sub-header-logo white spyscroll" data-custom="true"></div>
    </div> -->
    <div class="title-parent side-bar-margin spyscroll dispel" data-not-equal="0" data-class="on">
      <h1>(:= $pageTitle :)</h1>
    </div>
  </div>
</section>
(: child
 
<footer class="site-footer secondary bg-l3">
  <div class="content content-v row">
    <div class="col s8">
      <small>© 2014-2016 <a href="http://sirideas.com" target="_blank" class="link">Sir Ideas, C. A.</a>, Todos los derechos reservados.</small><span><small class="hide show-s">&nbsp;|&nbsp;<a class="link" target="_blank" href="https://github.com/SirIdeas/amathista/blob/master/LICENSE">MIT License</a>, <a class="link" target="_blank" href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0.</a>
      </small></span>
    </div>
    <div class="col s4 hide-v text-right">
      <small>
        <a class="link" target="_blank" href="https://github.com/SirIdeas/amathista/blob/master/LICENSE">MIT License</a>, <a class="link" target="_blank" href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0.</a>
      </small>
    </div>
  </div>
</footer>