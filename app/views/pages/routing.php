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
    Amathista utiliza por defecto la extensión <code><strong>AmRoute</strong></code> (<code><strong>ext/am_route</strong></code>) para determinar como despachar las peticiones HTTP. <code><strong>AmRoute</strong></code> obtiene las rutas configuradas de la propiedad <code><strong>routing</strong></code>.
  </p>

  <span>/app/routing.conf.php</span>
  <pre><code class="language-php">(:= getCodeFile('/routing/routing.php') :)</code></pre>

  <div>
    <h3>Estructura de las rutas</h3>
    <p>
      Las rutas poseen 3 partes:
    </p>
    <ul>
      <li>
        <code><strong>route</strong></code>: String con el formato de la ruta.
      </li>
      <li>
        <code><strong>routes</strong></code>: Array de rutas hijas, las cuales se forman concatenando los valores de la ruta padre en cada ruta hija.
      </li>
      <li>
        <strong>Tipos</strong>: Puede ser uno o varios y están representados por cada índice extra que contenga la ruta aparte de <code><strong>route</strong></code> y <code><strong>routes</strong></code>. Indican las formas en las que se puede despachar la ruta.
      </li>
    </ul>
  </div>

  <div>
    <h3>Forma simple o Forma explícita de las rutas</h3>

    <p>
      Una ruta puede ser presentado de diferentes formas, sin embargo al final todas son convertidaas a una a la <i>forma explícita</i>. Por ejemplo:
    </p>
    <pre><code class="language-php">(:= getCodeFile('/routing/forms.php') :)</code></pre>
  </div>

  <div>
    <h3>Tipos de rutas</h3>
    <p>
      Inicialmente se puede configurar los siguientes tipos de rutas
    </p>

    <div>
      <h4>Reponder con un archivo: <code>file</code></h4>
      <pre><code class="language-php">(:= getCodeFile('routing/file.php') :)</code></pre>
    </div>

    <div>
      <h4>Responder con la descarga de un archivo: <code>download</code></h4>
      <pre><code class="language-php">(:= getCodeFile('routing/download.php') :)</code></pre>
    </div>

    <div>
      <h4>Renderizar un template: <code>template</code></h4>
      <pre><code class="language-php">(:= getCodeFile('routing/template.php') :)</code></pre>
    </div>

    <div>
      <h4>Realizar la llamada de una función o método: <code>call</code></h4>
      <pre><code class="language-php">(:= getCodeFile('routing/call.php') :)</code></pre>
    </div>

    <div>
      <h4>Redirigir a otra URL de la aplicación: <code>redirect</code></h4>
      <pre><code class="language-php">(:= getCodeFile('routing/redirect.php') :)</code></pre>
    </div>

    <div>
      <h4>Rediriguir a una URL externa: <code>goto</code></h4>
      <pre><code class="language-php">(:= getCodeFile('routing/goto.php') :)</code></pre>
    </div>

  </div>

  <div>
    <h3>Rutas Anidadas</h3>
    <pre><code class="language-php">(:= getCodeFile('routing/nested.php') :)</code></pre>
  </div>

  <div>
    <h3>Parámetros de ruta</h3>
    <p>
      
    </p>
  </div>

  <div>
    <h3>Despachadores de rutas</h3>
    <p>
      Los despachadores son callbacks que se encargan de atender las peticiones HTTP según el tipo de ruta con el que coincidan.
    </p>
    <p>
      El despachador recive 3 argumentos:
    </p>
    <ul>
      <li>
        <code><strong>$detiny</strong></code>: Valor del índice correspondiente al tipo de ruta evaluado con los parámetros de la petición sustituidos.
      </li>
      <li>
        <code><strong>$env</strong></code>: Array asociativo con las variales de entorno de la propiedad de aplicación <code><strong>env</strong></code>.
      </li>
      <li>
        <code><strong>$params</strong></code>: Array asociativo con los parámetros obtenidos de la petición según indique el formato de la ruta, excluyendo aquellos que se encontraron dentro del argumento <code><strong>$destiny</strong></code>.
      </li>
    </ul>
    <p>
      Debe retorna <code><strong>true</strong></code> si logra despachar satisfactoriamente la petición, de lo contrario debe retornar <code><strong>false</strong></code> para indicar que se debe seguir intentando con otros despachadores.
    </p>
    <p>
      Para agregar o sustituir un despachador se utiliza el evento <code><strong>route.addDispatcher</strong></code> como se muestra a continuación.
    </p>
    <span>/app/am.init.php</span>
    <pre><code class="language-php">(:= getCodeFile('routing/dispatchers.php') :)</code></pre>
    <p>
      Así entonces se puede atender la siguiente ruta:
    </p>
    <pre><code class="language-php">(:= getCodeFile('routing/dispatchers-routes.php') :)</code></pre>

  </div>

  <div>
    <h3>Pre-procesadores de ruta</h3>
    <p>
      Los pre-procesadores de rutas son callbacks que reparan una ruta antes de que esta sea evaluada. Estos  son asignados a un tipo de ruta. Reciben como primer parámetro la ruta en forma explícita y debe retornar la ruta transformada.
    </p>
    <p>
      Para agregar el pre-procesador de ruta se utiliza el evento <code><strong>route.addPreProcessor</strong></code> como se muestra a continuación:
    </p>
    <span>/app/am.init.php</span>
    <pre><code class="language-php">(:= getCodeFile('routing/preprocessor.php') :)</code></pre>
    <p>
      Lo que transformaría las rutas de esto:
    </p>
    <pre><code class="language-php">(:= getCodeFile('routing/preprocessor-routing.php') :)</code></pre>
    <p>
      a esto:
    </p>
    <pre><code class="language-php">(:= getCodeFile('routing/preprocessor-result.php') :)</code></pre>
  </div>

  <div>
    <h3>Flujo de petición</h3>
    <p>
      El proceso seguido para determinar como despachar un ruta es el siguiente:
    </p>
    <ul class="nested-list">
      <li><strong>Inicio</strong></li>
      <li>Obtener <strong>petición</strong></li>
      <li>Por cada <strong>ruta</strong>
        <ul>
          <li>Llamar preprocesadores correspondientes a la ruta</li>
          <li>Si el formato de la <strong>ruta</strong> coincide con el de la <strong>petición</strong>
            <ul>
              <li>Por cada <strong>tipo</strong> de la <strong>ruta</strong>
                <ul>
                  <li>Si existe un despachador para el <strong>tipo</strong>
                    <ul>
                      <li>Preparar los parámetros</li>
                      <li>Llamar el despachador del <strong>tipo</strong></li>
                      <li>Si el llamado devolvió <code>true</code>
                        <ul>
                          <li>Ir al <strong>Fin</strong></li>
                        </ul>
                      </li>
                    </ul>
                  </li>
                </ul>
              </li>
            </ul>
          </li> 
        </ul>
      </li>
      <li>Responder con error 404</li>
      <li><strong>Fin</strong></li>
    </ul>
  </div>

</div>