(:: parent:views/base.php :)
(:: set:pageTitle='Amathista Framework' :)

<div id="pageTitle" class="primary bg-d1 box-shadow-2">
  <div class="content inner spyscroll" data-not-equal="0" data-class="dispel">
    <div class="title-parent">
      <h1>(:= $pageTitle :)</h1>
    </div>
  </div>
</div>

<div class="content">
  <div class="row">
    <div class="col s3">

      <ul class="nav spyscroll" data-height="#pageTitle" data-str="float">
        <li><a href="(:/:)">Inicio</a></li>
        <li><span>Primeros pasos</span>
          <ul class="sub-nav">
            <li><a href="(:/:)/introduction">Introducción</a></li>
            <li><a href="(:/:)/get-started">Comenzando</a></li>
            <li><a href="(:/:)/routing">Rutas</a></li>
            <li><a href="(:/:)/views">Vistas</a></li>
            <li><a href="(:/:)/controllers">Controladores</a></li>
          </ul>
        </li>
        <li>
          <a href="(:/:)/configure-server#">Configuración del servidor</a>
          <ul class="sub-nav">
            <li><a href="(:/:)/configure-server#apache">Apache</a></li>
            <li><a href="(:/:)/configure-server#nginx">Nginx</a></li>
          </ul>
        </li>
      </ul>
      <span class="">&nbsp;</span>

    </div>
    <div class="col s9 inner">
      (:: child :)
    </div>
  </div>
</div>