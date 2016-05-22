(: $menu=array()

<div class="title-parent">
  <a href="(:/:)">
    <img src="(:/:)/images/am_vlogo_p.png" alt="" class="sub-header-logo">
  </a>
</div>

<ul class="nav spyscroll" data-height="#pageTitle" data-str="float">
  (: foreach($menu as $url => $item):
    <li>
      <a href="(:/:)(:= $url :)">
        (:= $item['txt']
      </a>
      <ul class="sub-nav">
        (: foreach($item['items'] as $subUrl => $subItem):
          <li>
            <a href="(:/:)(:= $subUrl :)">
              (:= $subItem['txt']
            </a>
          </li>
        (: endforeach
      </ul>
    </li>
  (: endforeach
  <!--
  <li>
    <a href="(:/:)/api">API</a>
    <ul class="sub-nav">
      <li><span><strong>Extensiones</strong></span>
        <ul class="sub-nav">
          <li><a href="(:/:)/exts/AmCoder">AmCoder</a></li>
          <li><a href="(:/:)/exts/AmController">AmController</a></li>
          <li><a href="(:/:)/exts/AmResponse">AmResponse</a></li>
          <li><a href="(:/:)/exts/AmRoute">AmRoute</a></li>
          <li><a href="(:/:)/exts/AmScheme">AmScheme</a></li>
          <li><a href="(:/:)/exts/AmTpl">AmTpl</a></li>
        </ul>
      </li>
      <li><span><strong>Clases</a></strong></span>
        <ul class="sub-nav">
          <li><a href="(:/:)/classes/Am">Am</a></li>
          <li><a href="(:/:)/classes/AmCallResponse">AmCallResponse</a></li>
          <li><a href="(:/:)/classes/AmCoder">AmCoder</a></li>
          <li><a href="(:/:)/classes/AmController">AmController</a></li>
          <li><a href="(:/:)/classes/AmError">AmError</a></li>
          <li><a href="(:/:)/classes/AmField">AmField</a></li>
          <li><a href="(:/:)/classes/AmFileResponse">AmFileResponse</a></li>
          <li><a href="(:/:)/classes/AmGenerator">AmGenerator</a></li>
          <li><a href="(:/:)/classes/AmModel">AmModel</a></li>
          <li><a href="(:/:)/classes/AmObject">AmObject</a></li>
          <li><a href="(:/:)/classes/AmQuery">AmQuery</a></li>
          <li><a href="(:/:)/classes/AmRedirectResponse">AmRedirectResponse</a></li>
          <li><a href="(:/:)/classes/AmForeignKey">AmForeignKey</a></li>
          <li><a href="(:/:)/classes/AmResponse">AmResponse</a></li>
          <li><a href="(:/:)/classes/AmRoute">AmRoute</a></li>
          <li><a href="(:/:)/classes/AmScheme">AmScheme</a></li>
          <li><a href="(:/:)/classes/AmTable">AmTable</a></li>
          <li><a href="(:/:)/classes/AmTemplateResponse">AmTemplateResponse</a></li>
          <li><a href="(:/:)/classes/AmTpl">AmTpl</a></li>
          <li><a href="(:/:)/classes/AmValidator">AmValidator</a></li>
          <li><a href="(:/:)/classes/MysqlScheme">MysqlScheme</a></li>
          <li><a href="(:/:)/classes/BitValidator">BitValidator</a></li>
          <li><a href="(:/:)/classes/CustomValidator">CustomValidator</a></li>
          <li><a href="(:/:)/classes/DatetimeValidator">DatetimeValidator</a></li>
          <li><a href="(:/:)/classes/DateValidator">DateValidator</a></li>
          <li><a href="(:/:)/classes/EmailValidator">EmailValidator</a></li>
          <li><a href="(:/:)/classes/EmptyValidator">EmptyValidator</a></li>
          <li><a href="(:/:)/classes/FloatValidator">FloatValidator</a></li>
          <li><a href="(:/:)/classes/InQueryValidator">InQueryValidator</a></li>
          <li><a href="(:/:)/classes/IntValidator">IntValidator</a></li>
          <li><a href="(:/:)/classes/InValidator">InValidator</a></li>
          <li><a href="(:/:)/classes/LenValidator">LenValidator</a></li>
          <li><a href="(:/:)/classes/MaxLenValidator">MaxLenValidator</a></li>
          <li><a href="(:/:)/classes/MaxValueValidator">MaxValueValidator</a></li>
          <li><a href="(:/:)/classes/MinLenValidator">MinLenValidator</a></li>
          <li><a href="(:/:)/classes/MinValueValidator">MinValueValidator</a></li>
          <li><a href="(:/:)/classes/NullValidator">NullValidator</a></li>
          <li><a href="(:/:)/classes/PhoneValidator">PhoneValidator</a></li>
          <li><a href="(:/:)/classes/RangeValidator">RangeValidator</a></li>
          <li><a href="(:/:)/classes/RegexValidator">RegexValidator</a></li>
          <li><a href="(:/:)/classes/TimestampValidator">TimestampValidator</a></li>
          <li><a href="(:/:)/classes/TimeValidator">TimeValidator</a></li>
          <li><a href="(:/:)/classes/UniqueValidator">UniqueValidator</a></li>
          <li><a href="(:/:)/classes/YearValidator">YearValidator</a></li>
        </ul>
      </li>
    </ul>
  </li>
  -->
</ul>