(:: parent:views/base.php :)
(:: set:pageTitle='Introducción' :)

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
    <table class="table striped">
      <thead>
        <tr>
          <th class="s3">Término</th>
          <th>Descripción</th>
        </tr>
      </thead>
      <tbody class="text-left">
        <tr>
          <th>Aplicación</th>
          <td>
            Unidad de software conformada por subrutinas, clases, vistas, recursos y configuraciones entre otros, dispuestos de tal forma que dan solución a un problema.
          </td>
        </tr>
        <tr>
          <th>Extensión</th>
          <td>
            Módulos de software que pueden ser incluidos mediante Amathista para agregar una funcionalidad.
          </td>
        </tr>
        <tr>
          <th>Modo <i>routing</i></th>
          <td>
            Ejecución de Amathista desde un servidor w  eb para atender peticiones HTTP.
          </td>
        </tr>
        <tr>
          <th>Modo <i>tasking</i></th>
          <td>
            Ejecución de Amathista desde la línea de comandos para ejecutar una tarea o interpretar comandos.
          </td>
        </tr>
        <tr>
          <th>Archivo de configuración</th>
          <td>
            Archivo de extensión <code><strong>.conf.php</strong></code> que retorna un hash con una determinada configuración.
          </td>
        </tr>
        <tr>
          <th>Archivo de configuración raíz</th>
          <td>
            Archivo de configuración <code><strong>am.conf.php</strong></code> que contiene la configuración básica de una extensión o aplicación y está ubicado en la carpeta raíz del mismo.
          </td>
        </tr>
        <tr>
          <th>Archivo de configuración principal</th>
          <td>
            Archivo de configuración raíz de la aplicación.
          </td>
        </tr>
        <tr>
          <th>Archivo de inicio</th>
          <td>
            Archivo <code><strong>am.init.php</strong></code> contiene al código de inicialización de una extensión o aplicación y está ubicado en la raíz del mismo.
          </td>
        </tr>
        <tr>
          <th>Archivo de inicio principal</th>
          <td>
            Archivo de inicio de la aplicación.
          </td>
        </tr>
        <tr>
          <th>Bootfile</th>
          <td>
            Archivo de arranque de Amathista. Se encarga de incluir el núcleo de Amathista, inicializar la aplicación y ejecutarla. Por lo general es el archivo /public/bootfile.php. Es el único archivo que se ejecuta fuera del Entorno de ejecución.
          </td>
        </tr>
        <tr>
          <th>Directorio raíz de la aplicación</th>
          <td>
            Directorio contenedor de código fuente de la applicación como lo son los controladores, vistas y modelos entre otros) y donde se ejecutará Amathista (no confundir con el Directorio público). Este es definido en el llamado del método <code><strong>Am::app()</strong></code> del <code><strong>bootfile</strong></code>, en el cual or defecto es el directorio <code><strong>/app/</strong></code>. Su estrucutra interna es definida a conveniencia.
          </td>
        </tr>
        <tr>
          <th>Directorio público de la aplicación</th>
          <td>
            Directorio de archivos públicos de la aplicación. Contiene el archivo de arranque y los recursos públicos como los son archivos javascript, hoja de estilos, imágenes y fuentes entre otros. Su estructura interna es definida a conveniencia.
          </td>
        </tr>
        <tr>
          <th>Directorio de Amathista</th>
          <td>
            Directorio del cual se incluye Amathista y contiene su código fuente.
          </td>
        </tr>
        <tr>
          <th>Callback</th>
          <td>
            Puede ser el nombre de una función, un método estático en formato de string (<code><strong>'Clase::metodo'</strong></code>) o formato array (<code><strong>array('Clase', 'metodo')</strong></code>) o un método de un objeto (<code><strong>array($obj, 'método')</strong></code>).
          </td>
        </tr>
      </tbody>
    </table>
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
        <p>
          Descarga directa de <a class="link" target="_blank" href="https://codeload.github.com/SirIdeas/amathista.php/zip/master">GitHub</a>, y se descomprime donde resulte conveniente.
        </p>
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
    <table class="table striped">
      <thead>
        <tr>
          <th class="s2">Directorio</th>
          <th>Uso</th>
        </tr>
      </thead>
      <tbody class="text-left">
        <tr>
          <th><code>/am/</code></th>
          <td>Directorio de Amathista.</td>
        </tr>
        <tr>
          <th><code>/app/</code></th>
          <td>Directorio raíz de la aplicación.</td>
        </tr>
        <tr>
          <th><code>/public/</code></th>
          <td>Directorio público de la aplicación</td>
        </tr>
      </tbody>
    </table>
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
    <table class="table striped">
      <thead>
        <tr>
          <th class="s3">Archivo</th>
          <th>Descripción</th>
        </tr>
      </thead>
      <tbody class="text-left">
        <tr>
          <th><code>/app/am.conf.php</code></th>
          <td>Archivo de configuración principal de la aplicación</td>
        </tr>
        <tr>
          <th><code>/app/routing.conf.php</code></th>
          <td>Archivo de rutas la aplicación.</td>
        </tr>
        <tr>
          <th><code>/app/views/index.php</code></th>
          <td>Vista index de la aplicación</td>
        </tr>
        <tr>
          <th><code>/public/bootfile.php</code></th>
          <td>Bootfile.</td>
        </tr>
        <tr>
          <th><code>/public/.htaccess</code></th>
          <td>Configuración de Apache para la aplicación.</td>
        </tr>
        <tr>
          <th><code>/public/serverblock.conf</code></th>
          <td>Configuración de Nginx para la aplicación.</td>
        </tr>
        <tr>
          <th><code>/public/404.html</code></th>
          <td>Vista para errores 404.</td>
        </tr>
        <tr>
          <th><code>/public/images/*</code></th>
          <td>Imágenes utilizadas en las vistas copiadas.</td>
        </tr>
      </tbody>
    </table>
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