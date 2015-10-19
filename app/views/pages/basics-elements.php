(:: parent:views/base.php :)
(:: set:pageTitle='Elementos básicos' :)

<div id="pageTitle" class="primary bg-d1 box-shadow-2">
  <div class="content inner spyscroll" data-neq="0" data-class="dispel">
    <h1>(:= $pageTitle :)</h1>
  </div>
</div>

<div class="content inner">

  <div id="extensions">
    <h2>Extensiones</h2>
    
  </div>

  <div id="modeTasking">
    <h2>Modo <i>tasking</i></h2>
    
  </div>

  <div id="modeRouting">
    <h2>Modo <i>routing</i></h2>
    
  </div>

  <div id="confFile">
    <h2>Archivo de configuración</h2>

    <p>
      La primera configuración de amathista se realiza en el archivo <code><strong>/app/am.conf.php</strong></code>. En este archivo de definen las <a class="link">extensiones</a> que utilizará la aplicación, variables de entorno y varios otras configuraciones.
    </p>
    <p>En el caso de el <code><strong>testsite</strong></code> que se acaba de crear se asigna una variable de entorno llamada <code><strong>siteName</strong></code> con el nombre del sitio y se indica que se incluirá la extensión <a class="link"><code><strong>ext/am_route</strong></code></a> (extensión encargada de realizar el enrutamiendo de la aplicación):</p>
    <pre><code class="language-php">(:= getCodeFile('configuration/am.conf.php') :)</code></pre>

  </div>

  <div id="initFile">
    <h2>Archivo de inicio</h2>
    
  </div>

  <div id="amClass">
    <h2>Class <code><strong>Am</strong></code></h2>
    
  </div>


</div>