(:: parent:views/base.php :)
(:: set:menu=Am::getProperty('menus') :)

<section class="site-body">
  <div class="content row">
    <div class="site-sidebar col col2">

      <div class="title-parent">
        <a href="(:/:)">
          <div class="sub-header-logo"></div>
        </a>
      </div>

      <ul class="nav">
        (: foreach($menu['sidebar'] as $url => $item): :)
          <li>
            <a href="(:= empty($url)?'#':Am::url($url) :)">
              (:= $item['txt'] :)
            </a>
            (: if(isset($item['items'])): :)
              <ul class="sub-nav">
                (: foreach($item['items'] as $subUrl => $subTxt): :)
                  <li>
                    <a href="(:= empty($subUrl)?'#':Am::url($subUrl) :)">
                      (:= $subTxt :)
                    </a>
                  </li>
                (: endforeach :)
              </ul>
            (: endif :)
          </li>
        (: endforeach :)
      </ul>

    </div>
    
    <div class="col os-s2 col8">
      <div class="inner">(:: child :)</div>
    </div>

    <div id="pageNav" class="col col2 spyscroll" data-height="#pageTitle" data-class="site-sidebar">
      <ul class="page-nav spyscroll" data-nav="body" data-class="active" data-relative="#pageTitle">
        (: foreach($menu[$subMenuItem] as $url => $item): :)
          <li>
            <a href="(:= $url :)">
              (:= $item['txt'] :)
            </a>
            (: if(isset($item['items'])): :)
              <ul>
                (: foreach($item['items'] as $subUrl => $subTxt): :)
                  <li>
                    <a href="(:= $subUrl :)">
                      (:= $subTxt :)
                    </a>
                  </li>
                (: endforeach :)
              </ul>
            (: endif :)
          </li>
        (: endforeach :)
      </ul>
    </div>

  </div>
</section>