(:: parent:views/base.php :)
(:: set:pageTitle='vistas' :)

<div id="pageTitle" class="primary bg-d1 box-shadow-2">
  <div class="content inner spyscroll" data-neq="0" data-class="dispel">
    <h1>(:= $pageTitle :)</h1>
  </div>
</div>

<div class="content inner">
  
  <h2>AmRoute</h2>
  <p>

    Amathista utiliza por defecto la extensión <code><strong>AmRoute</strong></code> (<code><strong>ext/am_route</strong></code>) para determinar como atender las peticiones HTTP. <code><strong>AmRoute</strong></code> hace uso del archivo de configuración <code><strong>/app/routing.conf.php</strong></code> que contiene el item <code><strong>routes</strong></code> que contiene un array con las rutas configuradas.
  </p>

  <div>
    <pre><code class="language-php">(:= getCodeFile('/routing/routing.php') :)</code></pre>
  </div>

  <div>
    <h3>Tipos de rutas</h3>
    <p>Inicialmente se puede configurar los siguientes tipos de rutas</p>
  </div>

  <div>
    <h4>Reponder con un archivo</h4>
    <p>
      Devuelve el archivo <code><strong>/app/pdf/documento.pdf</strong></code>
    </p>
    <pre><code class="language-php">(:= getCodeFile('routing/file.php') :)</code></pre>
  </div>

  <div>
    <h4>Responder con la descarga de un archivo</h4>
    <p>
      Descarga el archivo <code><strong>/app/zips/documento.zip</strong></code> 
    </p>
    <pre><code class="language-php">(:= getCodeFile('routing/download.php') :)</code></pre>
  </div>

  <div>
    <h4>Redirigir a otra URL de la aplicación</h4>
    <p>
      Redirigue a la ruta interna <code><strong>/otraRuta</strong></code>.
    </p>
    <pre><code class="language-php">(:= getCodeFile('routing/redirect.php') :)</code></pre>
  </div>

  <div>
    <h4>Rediriguir a una URL externa</h4>
    <pre><code class="language-php">(:= getCodeFile('routing/goto.php') :)</code></pre>
  </div>

  <div>
    <h4>Renderizar un template</h4>
    <p>
      Renderiza el template <code><strong>/app/views/index.php</strong></code>
    </p>
    <pre><code class="language-php">(:= getCodeFile('routing/template.php') :)</code></pre>
  </div>

  <div>
    <h4>Realizar la llamada de una función o método</h4>
    <pre><code class="language-php">(:= getCodeFile('routing/call.php') :)</code></pre>
  </div>

  <div>
    
    <h3>Rutas Anidadas</h3>
    <!-- AmRoute::precallbacks -->
    <!-- AmRoute::set atend callback -->
  </div>

  <div>
    <h3>Pre-callbacks</h3>
  </div>

  <div>
    <h3>Asociando callbacks a tipos de rutas</h3>
  </div>

</div>