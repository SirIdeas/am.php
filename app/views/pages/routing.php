(:: parent:views/base.php :)
(:: set:pageTitle='Rutas' :)

<div id="pageTitle" class="primary bg-d1 box-shadow-2">
  <div class="content inner spyscroll" data-neq="0" data-class="dispel">
    <h1>(:= $pageTitle :)</h1>
  </div>
</div>

<div class="content inner">
  
  <p>
    Las rutas son configuradas en la propiedad de aplicación <code><strong>routing</strong></code>.
  </p>

  <div>
    <h2>Configuración</h2>
    
    <p>
      Las configuración de las rutas son tomados de la propiedad de aplicación <code><strong>routing</strong></code>:
    </p>

    <div class="code-row">
      <table>
        <tr>
          <td class="s6">
            <pre><code class="language-php">(:= getCodeFile('routing/routing.conf.php') :)</code></pre>
            <div></div>
          </td>
          <td class="s6">
            <pre><code class="language-php">(:= getCodeFile('routing/am.conf.php') :)</code></pre>
            <div></div>
          </td>
        </tr>
      </table>
    </div>

  </div>

  <div>
    <h2>Estructura de las rutas</h2>
    <p>
      Las rutas poseen 3 partes:
    </p>
    <table class="table striped">
      <thead>
        <tr>
          <th class="s2">Parte</th>
          <th>Descripcón</th>
        </tr>
      </thead>
      <tbody class="text-left">
        <tr>
          <td><code><strong>route</strong></code></td>
          <td>String con el formato de la ruta.</td>
        </tr>
        <tr>
          <td><code><strong>routes</strong></code></td>
          <td>
            Array de rutas hijas, las cuales se forman concatenando los valores de la ruta padre en cada ruta hija.
          </td>
        </tr>
        <tr>
          <th>Tipos</th>
          <td>
            Puede ser uno o varios y están representados por cada índice extra que contenga la ruta aparte de <code><strong>route</strong></code> y <code><strong>routes</strong></code>. Indican el blanco (target) de la ruta.
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div>
    <h2>Forma simple o Forma explícita de las rutas</h2>

    <p>
      Una ruta puede ser presentado de diferentes formas, sin embargo al final todas son convertidaas a una a la <i>forma explícita</i>. Por ejemplo:
    </p>
    <pre><code class="language-php">(:= getCodeFile('routing/forms.php') :)</code></pre>
  </div>

  <div>
    <h2>Tipos de rutas</h2>
    <p>
      Inicialmente se puede configurar los siguientes tipos de rutas
    </p>

    <div>
      <h3>Reponder con un archivo: <code>file</code></h3>
      <pre><code class="language-php">(:= getCodeFile('routing/file.php') :)</code></pre>
    </div>

    <div>
      <h3>Responder con la descarga de un archivo: <code>download</code></h3>
      <pre><code class="language-php">(:= getCodeFile('routing/download.php') :)</code></pre>
    </div>

    <div>
      <h3>Renderizar un template: <code>template</code></h3>
      <pre><code class="language-php">(:= getCodeFile('routing/template.php') :)</code></pre>
    </div>

    <div>
      <h3>Redirigir a otra URL de la aplicación: <code>redirect</code></h3>
      <pre><code class="language-php">(:= getCodeFile('routing/redirect.php') :)</code></pre>
    </div>

    <div>
      <h3>Rediriguir a una URL externa: <code>goto</code></h3>
      <pre><code class="language-php">(:= getCodeFile('routing/goto.php') :)</code></pre>
    </div>

    <div>
      <h3>Realizar la llamada de una función o método: <code>call</code></h3>
      <pre><code class="language-php">(:= getCodeFile('routing/call.php') :)</code></pre>
      <p>
        Todos los estos callbacks reciben como parámetro un array con el entorno definido en la propiedad de aplicación <code><strong>env</strong></code>.
      </p>
    </div>

  </div>

  <div>
    <h2>Rutas Anidadas</h2>
    <pre><code class="language-php">(:= getCodeFile('routing/nested.php') :)</code></pre>
  </div>

  <div>
    <h2>Parámetros de ruta</h2>
    <p>
      Los parámetros pueden ser definidos entre llaves en ruta y serán sustituidos en <i>target</i>, Por ejemplo, para la ruta:
    </p>
    <pre><code class="language-php">(:= getCodeFile('routing/params.php') :)</code></pre>
    <p>
      una petición <code><strong>/models/user/edit/3</strong></code>, renderizaría el template <code><strong></strong></code> y dentro de esta vista exisitirían las variables <code><strong>$model = 'user'</strong></code> y <code><strong>$id = '3'</strong></code>.
    </p>
    <p>
      En el caso de que la ruta realice el llamado de un callback (ruta del tipo <code><strong>call</strong></code>), este recibirá como parámetros cada uno de los definidos en la ruta, agregando el parámetro extra con el entorno definido en la propiedad de aplicación <code><strong>env</strong></code>, por ejemplo:
    </p>
    <pre><code class="language-php">(:= getCodeFile('routing/params-callbacks.php') :)</code></pre>
    <div>
      <h3>Tipos de los parámetros de ruta</h3>
      <p>
        Los tipos para los parámetros de ruta son definidos despues del nombre del parámetro con dos puntos (:). Los tipos principales son <code><strong>id</strong></code>, <code><strong>numeric</strong></code>, <code><strong>alphabetic</strong></code> y <code><strong>alphanumeric</strong></code>, sin embargo tambien puede definirse definirse una regex:
      </p>
      <pre><code class="language-php">(:= getCodeFile('routing/params-types.php') :)</code></pre>

    </div>
  </div>
  
  <div>
    <h2>Opciones avanzadas</h2>

    <div>
      <h3>Despachadores de rutas</h3>
      <p>
        Los despachadores son callbacks que se encargan de atender las peticiones HTTP según el tipo de ruta con el que coincidan.
      </p>
      <p>
        El despachador recive 3 argumentos:
      </p>


      <table class="table striped">
        <thead>
          <tr>
            <th class="s2">Parte</th>
            <th>Descripcón</th>
          </tr>
        </thead>
        <tbody class="text-left">
          <tr>
            <td><code><strong>$target</strong></code></td>
            <td>
              Valor del índice correspondiente al tipo de ruta evaluado con los parámetros de la petición sustituidos.
            </td>
          </tr>
          <tr>
            <td><code><strong>$env</strong></code></td>
            <td>
              Hash con las variales de entorno de la propiedad de aplicación <code><strong>env</strong></code>.
            </td>
          </tr>
          <tr>
            <th><code><strong>$params</strong></code></th>
            <td>
              Hash con los parámetros obtenidos de la petición según indique el formato de la ruta.
            </td>
          </tr>
        </tbody>
      </table>
      <p>
        Debe retorna <code><strong>true</strong></code> si logra despachar satisfactoriamente la petición, de lo contrario debe retornar <code><strong>false</strong></code> para indicar que se debe seguir intentando con otros despachadores.
      </p>
      <p>
        Para agregar o sustituir un despachador se utiliza el método <code><strong>Am::addRouteDispatcher</strong></code>:.
      </p>
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
        Para agregar el pre-procesador de ruta se utiliza el método <code><strong>Am::addRoutePreProcessor</strong></code>:
      </p>
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
                        <li>Si el llamado se ejecutó satisfactoriamente
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

</div>