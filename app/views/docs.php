(:: parent:views/base.php :)
(:: set:menu=Am::getProperty('menus') :)

<section class="site-body">
  <div class="content row">
    <div class="site-sidebar col col2">

      <div class="title-parent">
        <a href="(:/:)">
          <img src="(:/:)/images/am_vlogo_p.png" alt="" class="sub-header-logo">
        </a>
      </div>

      <ul class="nav spyscroll" data-height="#pageTitle" data-str="float">
        (: foreach($menu['sidebar'] as $url => $item): :)
          <li>
            <a href="(:/:)(:= $url :)">
              (:= $item['txt'] :)
            </a>
            <ul class="sub-nav">
              (: foreach($item['items'] as $subUrl => $subTxt): :)
                <li>
                  <a href="(:/:)(:= $subUrl :)">
                    (:= $subTxt :)
                  </a>
                </li>
              (: endforeach :)
            </ul>
          </li>
        (: endforeach :)
      </ul>

    </div>
    
    <div class="col os-s2 col8">
      <div class="inner">(:: child :)</div>
    </div>

    <div class="site-sidebar col col2">
      <ul class="page-nav">
        (: foreach($menu[$subMenuItem] as $url => $item): :)
          <li>
            <a href="(:= $url :)">
              (:= $item['txt'] :)
            </a>
            <ul class="sub-nav">
              (: if(isset($item['items'])): :)
                (: foreach($item['items'] as $subUrl => $subTxt): :)
                  <li>
                    <a href="(:= $subUrl :)">
                      (:= $subTxt :)
                    </a>
                  </li>
                (: endforeach :)
              (: endif :)
            </ul>
          </li>
        (: endforeach :)
      </ul>
    </div>

  </div>
</section>