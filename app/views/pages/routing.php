(:: parent:views/base.php :)
(:: set:pageTitle='Rutas' :)

<div id="pageTitle" class="primary bg-d1 box-shadow-2">
  <div class="content inner spyscroll" data-neq="0" data-class="dispel">
    <h1>(:= $pageTitle :)</h1>
  </div>
</div>

<div class="content inner">
  
  <h2>AmRoute</h2>
  <p>
    Amathista utiliza por defecto la extensión <code><strong>AmRoute</strong></code> (<code><strong>ext/am_route</strong></code>) para determinar como despachar las peticiones HTTP. <code><strong>AmRoute</strong></code> obtiene las rutas configuradas del item <code><strong>routes</strong></code> de la propiedad <code><strong>routing</strong></code>.
  </p>

  <div>
    <span>/app/routing.conf.php</span>
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
    <p>
      De igual forma se pueden anidar rutas haciendo uso de la forma explicita mediante el item <code><strong>routes</strong></code>. Por ejemplo:
    </p>
    <pre><code class="language-php">(:= getCodeFile('routing/nested.php') :)</code></pre>
  </div>

  <div>
    <h3>Pre-callbacks de ruta</h3>
    <p>
      Son callbacks preparan una ruta antes de que esta sea evaluada. Estos son llamados siempre y cuando la ruta a evaluar contenga el item al que se haya asignado. Reciben como primer parámetro la ruta en forma explícita y debe retornar la ruta transformada.
    </p>
    <p>
      Para agregar el pre callback de ruta se debe disparar el evento <code><strong>route.addPreCallback</strong></code> como se muestra a continuación:
    </p>
    <span>/app/am.init.php</span>
    <pre><code class="language-php">(:= getCodeFile('routing/precallback.php') :)</code></pre>
    <p>
      Lo que transformaría las rutas:
    </p>
    <pre><code class="language-php">(:= getCodeFile('routing/precallback-routing.php') :)</code></pre>
  </div>

  <div>
    <h3>Personalización de los tipos de rutas</h3>
    <p style="color:red">pendiente</p>
  </div>

  <div>
    <h3>Prioridad entre tipos de ruta</h3>
    <p style="color:red">pendiente</p>
  </div>

  <div>
    <h3>Transformación de una ruta simple a explicita</h3>
    <p style="color:red">pendiente</p>
  </div>

</div>