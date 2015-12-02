(:: parent:views/base.php :)
(:: set:pageTitle='Elementos básicos' :)

<div id="pageTitle" class="primary bg-d1 box-shadow-2">
  <div class="content inner spyscroll" data-neq="0" data-class="dispel">
    <h1>(:= $pageTitle :)</h1>
  </div>
</div>

<div class="content inner">
  
  <p>
    Documentación de los elementos básicos de Amathista.
  </p>

  <div id="terminos">
    <h2>Términos</h2>
    <p>
      A continuación se definen alguno de los términos utilizados en la documentación:
    </p>
    <ul>
      <li>
        <p>
          <strong>Aplicación</strong>: Unidad de software conformada por subrutinas, clases, vistas, recursos y configuraciones entre otros, dispuestos de tal forma que dan solución a un problema.
        </p>
      </li>
      <li>
        <p>
          <strong>Extensión</strong>: Módulos de software que pueden ser incluidos mediante Amathista para agregar una funcionalidad.
        </p>
      </li>
      <li>
        <p>
          <strong>Modo <i>routing</i></strong>: Ejecución de Amathista desde un servidor w  eb para atender peticiones HTTP.
        </p>
      </li>
      <li>
        <p>
          <strong>Modo <i>tasking</i></strong>: Ejecución de Amathista desde la línea de comandos para ejecutar una tarea o interpretar comandos.
        </p>
      </li>
      <li>
        <p>
          <strong>Archivo de configuración</strong>: Archivo de extensión <code><strong>.conf.php</strong></code> que retorna un array asociativo con una determinada configuración.
        </p>
      </li>
      <li>
        <p>
          <strong>Archivo de configuración raíz</strong>: Archivo de configuración <code><strong>am.conf.php</strong></code> que contiene la configuración básica de una extensión o aplicación y está ubicado en la carpeta raíz del mismo.
        </p>
      </li>
      <li>
        <p>
          <strong>Archivo de inicio</strong>: Archivo <code><strong>am.init.php</strong></code> contiene al código de inicialización de una extensión o aplicación y está ubicado en la raíz del mismo.
        </p>
      </li>
    </ul>
  </div>

  <div id="mainConfFile">
    <h2>Archivo de configuración principal <small>(<code>/app/am.conf.php</code>)</small></h2>
        
    <p>
      Archivo de configuiración de la aplicación. En este archivo se indica las extensiones a cargar, archivos inciales, configuraciones de los módulos a reescribir y variables de entorno de toda la aplicación. Este está ubicado en la <code><strong>/app/</strong></code> de la aplicación.
    </p>
    <p>
      El <code><strong>samplesite</strong></code> que se acaba de crear el archivo de configuración declara una variable de entorno llamada <code><strong>siteName</strong></code> con el nombre del sitio y se indica que se incluirá la extensión <code><strong>ext/am_route</strong></code>, extensión encargada de realizar el enrutamiendo de la aplicación.
    </p>
    <pre><code class="language-php">(:= getCodeFile('configuration/am.conf.php') :)</code></pre>

  </div>

  <div id="mainInitFile">
    <h2>Archivo de inicio principal</h2> <small>(<code>/app/am.init.php</code>)</small></h2>
    <p>
      Archivo PHP que se ejecuta luego de incluir las extensiones configuradas en el Archivo de configuración principal. Si fin es el declarar las clases, funciones, constantes y variables globales entre otros de la aplicación.
    </p>
  </div>

  <div id="routing">
    <h2>Rutas</h2>
    <p>
      Amathista utiliza por defecto la extensión <code><strong>AmRoute</strong></code> (<code><strong>ext/am_route</strong></code>) para atender las peticiones HTTP. <code><strong>AmRoute</strong></code> hace uso del archivo de configuración <code><strong>/app/routing.conf.php</strong></code> el cual retorna un array asociativo con item <code><strong>routes</strong></code> que contiene un array con las rutas configuradas.
    </p>
    <pre><code class="language-php">(:= getCodeFile('configuration/routing.conf.php') :)</code></pre>

    <p>Inicialmente se puede configurar los siguientes tipos de rutas</p>
    <ul>
      <li>
        <p>
          <code><strong>file</strong></code>: Responder con un archivo (/app/pdf/documento.pdf).
        </p>
        <pre><code class="language-php">(:= getCodeFile('configuration/routing-file.conf.php') :)</code></pre>
      </li>
      <li>
        <p>
          <code><strong>download</strong></code>: Responder con la descarga de un archivo (/app/zips/documento.zip).
        </p>
        <pre><code class="language-php">(:= getCodeFile('configuration/routing-download.conf.php') :)</code></pre>
      </li>
      <li>
        <p>
          <code><strong>redirect</strong></code>: Rediriguir a una URL de la aplicación
        </p>
        <pre><code class="language-php">(:= getCodeFile('configuration/routing-redirect.conf.php') :)</code></pre>
      </li>
      <li>
        <p>
          <code><strong>goto</strong></code>: Rediriguir a una URL externa
        </p>
        <pre><code class="language-php">(:= getCodeFile('configuration/routing-goto.conf.php') :)</code></pre>
      </li>
      <li>
        <p>
          <code><strong>template</strong></code>: Renderiza un template
        </p>
        <pre><code class="language-php">(:= getCodeFile('configuration/routing-template.conf.php') :)</code></pre>
      </li>
      <li>
        <p>
          <code><strong>call</strong></code>: Realizar la llamada de una función o método estático.
        </p>
        <pre><code class="language-php">(:= getCodeFile('configuration/routing-call.conf.php') :)</code></pre>
      </li>
    </ul>

  </div>



</div>