(: parent:views/base.php :)
(: set:pageTitle='Introducción' :)

<div id="pageTitle" class="primary bg-d1 box-shadow-2">
  <div class="content inner spyscroll" data-neq="0" data-class="dispel">
    <h1>(:= $pageTitle :)</h1>
  </div>
</div>
<div class="content inner">
  
  <div>
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
          <strong>Archivo de configuración principal</strong>: Archivo de configuración raíz de la aplicación.
        </p>
      </li>
      <li>
        <p>
          <strong>Archivo de inicio</strong>: Archivo <code><strong>am.init.php</strong></code> contiene al código de inicialización de una extensión o aplicación y está ubicado en la raíz del mismo.
        </p>
      </li>
      <li>
        <p>
          <strong>Archivo de inicio principal</strong>: Archivo de inicio de la aplicación.
        </p>
      </li>
      <li>
        <p>
          <strong>Bootfile</strong> Archivo de arranque de Amathista. Se encarga de incluir el núcleo de Amathista, inicializar la aplicación y ejecutarla. Por lo general es el archivo /public/bootfile.php. Es el único archivo que se ejecuta fuera del Entorno de ejecución.
        </p>
      </li>
      <li>
        <p>
          <strong>Directorio raíz de la aplicación</strong>: Directorio contenedor de código fuente de la applicación como lo son los controladores, vistas y modelos entre otros) y donde se ejecutará Amathista (no confundir con el Directorio público). Este es definido en el llamado del método <code><strong>Am::app()</strong></code> del <code><strong>bootfile</strong></code>, en el cual or defecto es el directorio <code><strong>/app/</strong></code>. Su estrucutra interna es definida a conveniencia.
        </p>
      </li>
      <li>
        <p>
          <strong>Directorio público de la aplicación</strong>: Directorio de archivos públicos de la aplicación. Contiene el archivo de arranque y los recursos públicos como los son archivos javascript, hoja de estilos, imágenes y fuentes entre otros. Su estructura interna es definida a conveniencia.
        </p>
      </li>
      <li>
        <p>
          <strong>Directorio de Amathista</strong>: Directorio del cual se incluye Amathista y contiene su código fuente.
        </p>
      </li>
      <li>
        <p>
          <strong>Callback</strong>: Puede ser el nombre de una función, un método estático en formato de string (<code><strong>'Clase::metodo'</strong></code>) o formato array (<code><strong>array('Clase', 'metodo')</strong></code>), un método de un objeto (<code><strong>array($obj, 'método')</strong></code>) o en algunas ocasiones una llamada a un controlador (<code><strong>'NombreControlador@accion'</strong></code>).
        </p>
      </li>
    </ul>
  </div>

  <div>
    <h2>Requerimientos</h2>

    <p>Para ejecutar Amathista solo se require PHP <strong>>=5.4</strong></p>
  </div>

  <div>
    <h2>Descarga</h2>

    <p>
      La descarga de Amathista se realiza desde GitHub ya sea como un archivo comprimido, o clonando el repositorio.
    </p>
    <ul>
      <li>
        Descarga directa de <a class="link" target="_blank" href="https://codeload.github.com/SirIdeas/amathista.php/zip/master">GitHub</a>, y se descomprime donde resulte conveniente.
      </li>
      <li>
        <p>
          Clonar desde GitHub en la carpeta que crea conveniente:
        </p>
        <pre><code class="language-bash">$ git clone https://github.com/SirIdeas/amathista.php.git</code></pre>
      </li>
    </ul>
  </div>

  <div>
    <h2>Estructura</h2>

    <p>
      La estructura básica de una aplicación en Amathista consiste de 3 carpetas principales:
    </p>
    <ul>
      <li>
        <p>
          <code><strong>/am/</strong></code>: Directorio de Amathista.
        </p>
      </li>
      <li>
        <p>
          <code><strong>/app/</strong></code>: Directorio raíz de la aplicación.
        </p>
      </li>
      <li>
        <p>
          <code><strong>/public/</strong></code>: Directorio público de la aplicación
        </p>
      </li>
    </ul>
    <p>
      Estas carpetas pueden tener el nombre que convenga e inclusive pueden estar ubicadas en lugares que se desee.
    </p>
    <p>
      Para efectos de la documentación estos directorios estarán ubicados al mismo nivel.
    </p>
  </div>
  
  <div>
    <h2>Archivos iniciales</h2>
    
    <p>
      La plantilla base de Amathista incluye otros archivos:
    </p>
    <ul>
      <li>
        <p>
          <code><strong>/app/am.conf.php</strong></code>: Archivo de configuración principal de la aplicación
        </p>
      </li>
      <li>
        <p>
          <code><strong>/app/routing.conf.php</strong></code>: Archivo de rutas la aplicación.
        </p>
      </li>
      <li>
        <p>
          <code><strong>/app/views/index.php</strong></code>: Vista index de la aplicación
        </p>
      </li>
      <li>
        <p>
          <code><strong>/public/bootfile.php</strong></code>: Bootfile.
        </p>
      </li>
      <li>
        <p>
          <code><strong>/public/.htaccess</strong></code>: Configuración de Apache para la aplicación.
        </p>
      </li>
      <li>
        <p>
          <code><strong>/public/serverblock.conf</strong></code>: Configuración de Nginx para la aplicación.
        </p>
      </li>
      <li>
        <p>
          <code><strong>/public/404.html</strong></code>: Vista para errores 404.
        </p>
      </li>
      <li>
        <p>
          <code><strong>/public/images/*</strong></code>: Imágenes utilizadas en las vistas copiadas.
        </p>
      </li>
    </ul>
  </div>

  <div>
    <h2>Sitio de pruebas</h2>

    <p>
      Para verificar que todo vaya bien se puede ejcutar el sitio desde un navegador. En nuestro caso creamos el proyecto dentro de la carpeta <code><strong>testsite</strong></code> dentro de la carpeta web de nuestra instalación local de WAMPP:
    </p>
    <img class="def-img" src="(:/:)/images/testfile-folder.jpg" alt="testfile-folder.jpg">

    <p>
      Ejecutamos el sitio en un navegador mediante la url <code><strong>http://localhost/testsite/public/</strong></code>:
    </p>
    <img class="def-img" src="(:/:)/images/testsite-first-look.jpg" alt="testsite-first-look.jpg">
  </div>

</div>