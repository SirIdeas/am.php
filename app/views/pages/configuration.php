(#: parent:views/base.php #)
(#: set:pageTitle='Configuración' #)

<div id="pageTitle" class="primary bg-d1 box-shadow-2">
  <div class="content inner spyscroll" data-neq="0" data-class="dispel">
    <h1>(#= $pageTitle #)</h1>
  </div>
</div>
<div class="content inner">

  <div id="requirements">
    <h2>Requerimientos</h2>

    <p>Para ejecutar Amathista solo se require PHP <strong>>=5.4</strong></p>

  </div>

  <div id="downloadFramework">
    <h2>Descargar framework</h2>

    <p>
      La descarga de Amathista se realiza desde GitHub ya se com un archivo comprimido, o clonando el repositorio.
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
  
  <div id="defineStructure">
    <h2>Definir estructura</h2>

    <p>
      La estructura básica de una aplicación recomendada consiste de 3 carpetas principales:
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
          <code><strong>/public/</strong></code>: Directorio raíz de la aplicación en el servidor. Conteine el archivo de arranque y los recursos públicos como los son archivos javascript, hoja de estilos en cascadas, imágenes y fuentes entre otros. Su estructura interna es definida a conveniencia.
        </p>
      </li>
    </ul>
    <p>
      Estas carpetas pueden tener el nombre que convenga e inclusive pueden estar ubicadas en lugares que se desee. Así mismo, estos pueden ser un único directorio, sin embargo, siempre es recomendable mantenerlos por separado.
    </p>
    <p>
      Para efectos de la documentación estos directorios estarán ubicados al mismo nivel.
    </p>

  </div>

  <div id="createBootfile">
    <h2>Crear el archivo de arranque (<i>bootfile</i>)</h2>

    <p>
      El <i>bootfile</i> es el archivo que inicia la ejecución de la aplicación en Amathista. Suele estar en la raíz del directorio <code><strong>/public/bootfile.php</strong></code> y se encarga de:
    </p>
    <ol>
      <li>
        <p>
          Personalizar el comportamiento de Amathista (mediante la definición de algunas constantes).
        </p>
      </li>
      <li>
        <p>
          Incluir el núcleo de Amathista.
        </p>
      </li>
      <li>
        <p>
          Inicializar la aplicación.
        </p>
      </li>
      <li>
        <p>
          Ejecutar la aplicación
        </p>
      </li>
    </ol>
    <p>
      El contenido inicial del archivo de arranque es el siguiente:
    </p>
    <pre><code class="language-php">(#= getCodeFile('configuration/bootfile.php') #)</code></pre>
    <p>
      La clase <code><strong>Am</strong></code> es la clase principal de Amathista a partir de la cual se incluyen todas las demás a medida que se van requiriendo.
    </p>
  </div>
  
  <div id="initApp">
    <h2>Inicializar aplicación (opcional)</h2>
    
    <p>
      Para copiar los recursos iniciales básicos de la aplicación basta con ejecutar dentro de la carpeta <code><strong>/public/</strong></code> la siguiente línea de comandos:
    </p>
    <pre><code class="language-bash">$ php bootfile.php create</code></pre>
    
    <p>
      Esto copiará en las carpetas <code><strong>/app/</strong></code> y <code><strong>/public/</strong></code> una serie de archivos y recursos que conformarán una aplicación base:
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
  
  <div id="configureServer">
    <h2>Configurar servidor</h2>

    <p>
      El servidor web debe ser configurado para tomar el directorio <code><strong>/public/</strong></code> como directorio base de la aplicación.
    </p>
    
    <h3>Apache</h3>
    <pre><code class="language-apacheconf">(#= getCodeFile('configuration/.htaccess') #)</code></pre>
    <p>
      <strong>Nota:</strong> Para el correcto funcinamiento con apache se requiere tener activado el mó'dulo'
    </p>

    <h3>Nginx</h3>
    <pre><code class="language-nginx">(#= getCodeFile('configuration/serverblock.conf') #)</code></pre>

  </div>

  <div id="testsite">
    <h2>Sitio de pruebas</h2>

    <p>
      Para verificar que todo vaya bien se puede ejcutar el sitio desde un navegador. En nuestro caso creamos el proyecto dentro de la carpeta <code><strong>testsite</strong></code> dentro de la carpeta web de nuestra instalación local de WAMPP:
    </p>
    <img class="def-img" src="(#/#)/images/testfile-folder.jpg" alt="testfile-folder.jpg">

    <p>
      Ejecutamos el sitio en un navegador mediante la url <code><strong>http://localhost/testsite/public/</strong></code>:
    </p>
    <img class="def-img" src="(#/#)/images/testsite-first-look.jpg" alt="testsite-first-look.jpg">
  
  </div>

  <div>
    <h2>¿Qué sigue?</h2>
    <p>
      Con esto queda configurada nuestra aplicación base. A partir de esta se irán explicando todos y cada unos de los módulos de Amathista Framework. El siguiente paso es estudiar los elementos básicos de una aplicación en Amathista.
    </p>
  </div>

</div>