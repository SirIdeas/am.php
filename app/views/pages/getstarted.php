(:: parent:views/base.php :)
(:: set:pageTitle='Comenzar' :)

<div id="pageTitle" class="primary bg-d1 box-shadow-2">
  <div class="content inner spyscroll" data-neq="0" data-class="dispel">
    <h1>(:= $pageTitle :)</h1>
  </div>
</div>
<div class="content inner">

  <div id="requirements">
    <h2>Requerimientos</h2>

    <p>Para ejecutar Amathista solo se require PHP <strong>>=5.4</strong></p>

  </div>

  <div id="download">
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
  
  <div id="structure">
    <h2>Estructura</h2>

    <p>
      La estructura básica de una aplicación en Amathista consiste de 3 carpetas principales:
    </p>
    <ul>
      <li>
        <p>
          <code><strong>/am/</strong></code>: Directorio contenedor de Amathista. Representa la carpeta donde se encuentra el código fuente de Amathista.
        </p>
      </li>
      <li>
        <p>
          <code><strong>/app/</strong></code>: Directorio contenedor de código fuente de la applicación como lo son los controladores, vistas y modelos entre otros). Su estrucutra interna es definida a conveniencia.
        </p>
      </li>
      <li>
        <p>
          <code><strong>/public/</strong></code>: Directorio raíz de la aplicación en el servidor. Contiene el archivo de arranque y los recursos públicos como los son archivos javascript, hoja de estilos, imágenes y fuentes entre otros. Su estructura interna es definida a conveniencia.
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
  
  <div id="initFiles">
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
          <code><strong>/public/bootfile.php</strong></code>: Archivo de arranque de Amathista. Se encarga de incluir el núcleo de Amathista, inicializar la aplicación y ejecutarla.
        </p>
      </li>
      <li>
        <p>
          <code><strong>/public/.htaccess</strong></code>: Configuración de <a href="#" class="link">Apache</a> para la aplicación.
        </p>
      </li>
      <li>
        <p>
          <code><strong>/public/serverblock.conf</strong></code>: Configuración de <a href="#" class="link">Nginx</a> para la aplicación.
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

  <div id="testsite">
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

  <div>
    <h2>¿Qué sigue?</h2>
    <p>
      Con esto ya tenemos una apicación con Amathista sobre la cual podemos empezar a trabajar. A partir ahora, se irán explicando cada uno de los módulos de Amathista Framework. El siguiente paso es estudiar los elementos básicos de una aplicación en Amathista.
    </p>
  </div>

</div>