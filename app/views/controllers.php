(: parent:'views/_docs.php'
(: $pageTitle = 'Controladores'
(: $subMenuItem = 'controllers'

<p>
  Los controladores son clases que poseen métodos llamados acciones, las cuales pueden ser asignadas como procesadores de las rutas. Además de esto, los controladores tambien poseen métodos que pueden ser configurados como filtros para las acciones, con el fin de validar su ejecución, realizar tareas antes y después de una acción. Además de todo esto, cada acción renderiza una vista con el mismo nombre de la acción, a menos que se indique lo contrario.
</p>

<div>
  <h2 id="routes">Rutas</h2>
  
  <p>
    Crear ruta con una acción de un controlador:
  </p>

  <pre><code class="language-php">(:= getCodeFile('controllers/routing.conf.php') :)</code></pre>

</div>

<div>
  <h2 id="configuration">Configuración</h2>

  <p>
    Las configuración de los controladores es tomada de la propiedad de aplicación <code><strong>controllers</strong></code>. Esta es un hash donde cada clave representa el nombre de un controlador y el valor su configuración.
  </p>

  <div class="row divide-section">
    <pre class="col s6"><code class="language-php">(:= getCodeFile('controllers/controllers.conf.php') :)</code></pre>
    <pre class="col s6"><code class="language-php">(:= getCodeFile('controllers/am.conf.php') :)</code></pre>
  </div>

</div>

<div>
  <h2 id="considerations">Consideraciones</h2>
  <ul>
    <li>
      Por defecto el directorio raíz de los controladores es <code><strong>/controllers/</strong></code> dentro del directorio raíz de la aplicación.
    </li>
    <li>
      Las clases de los controladores deben extender de la clase <code><strong>AmController</strong></code> o de cualquier otro controlador.
    </li>
    <li>
      Por defecto las acciones de los controladores están representadas por los métodos del mismo con el prefijo <code><strong>action_</strong></code>.
    </li>
    <li>
      Por defecto el directorio de vistas de los controladores es la carpeta <code><strong>views</strong></code> dentro de la carpeta raíz del controlador.
    </li>
    <li>
      Las acciones renderizan automáticamente la vista con el mismo nombre de la acción y extensión <code><strong>.php</strong></code> dentro del directorio de vistas.
    </li>
  </ul>
</div>

<div>

  <h2 id="properties">Propiedades</h2>
  
  <p>
    Las propiedades configurables son:
  </p>

  <div>
    <h3 id="property-views">Directorio principal de vistas</h3>

    <table class="table striped small text-left">
      <tr><th>Propiedad</th><td><code><strong>views</strong></code></td></tr>
      <tr><th>Tipo</th><td><code><strong>string</strong></code></td></tr>
      <tr>
        <th>Valor por defecto</th>
        <td><pre class="table-pre"><code class="language-php">'views' => 'views'</code></pre></td>
      </tr>
    </table>

    <p>
      Directorio relativo al directorio raíz del controlador donde se comenzará a buscar la vista correspondientes a cada acción.
    </p>

    <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/views.controllers.conf.php') :)</code></pre>

    <p>
      Si el controlador <code><strong>Foo</strong></code> está ubicado en el directorio <code><strong>/app/controllers</strong></code> entonces el directorio principal de vistas será <code><strong>/app/controllers/vistas/</strong></code>.
    </p>

  </div>

  <div>
    <h3 id="property-paths">Directorios secundarios de vistas</h3>

    <table class="table striped small text-left">
      <tr><th>Propiedad</th><td><code><strong>paths</strong></code></td></tr>
      <tr><th>Tipo</th><td><code><strong>array(string)</strong></code></td></tr>
      <tr>
        <th>Valor por defecto</th>
        <td><pre class="table-pre"><code class="language-php">'paths' => array()</code></pre></td>
      </tr>
    </table>
    <p>
      Lista de directorios secundarios ordenados por prioridad donde se buscarán las vistas de las acciones del controlador. A diferencia del directorio <code><strong>views</strong></code> que es relativo al directorio raíz de la controlador, estos directorios deben ser realitivos al directorios raíz de la aplicación. Un controlador con la propiedad que herede de otro controlador hereda el directorio <code><strong>views</strong></code> y los paths del padre como directorios dentro esta propiedad.
    </p>
    <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/paths.controllers.conf.php') :)</code></pre>

  </div>

  <div>
    <h3 id="property-prefixs">Prefijos</h3>

    <table class="table striped small text-left">
      <tr><th>Propiedad</th><td><code><strong>prefixs</strong></code></td></tr>
      <tr><th>Tipo</th><td><code><strong>hash(string)</strong></code></td></tr>
      <tr>
        <th>Valor por defecto</th>
        <td><pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/prefixs.php') :)</code></pre></td>
      </tr>
    </table>

    <p>
      Prefijos utilizados para identificar cada tipo de métodos del controlador. Básicamente posee dos tipos: <strong>acciones</strong> que puede ser solicitadas por los diferentes requests methods y <strong>filtros</strong> que puede ser ejecutados antes y/o después del método de una acción.
    </p>

    <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/prefixs.Foo.php') :)</code></pre>

  </div>

  <div>
    <h3 id="property-allows">Acciones permitidas</h3>

    <table class="table striped small text-left">
      <tr><th>Propiedad</th><td><code><strong>allows</strong></code></td></tr>
      <tr><th>Tipo</th><td><code><strong>hash(bool|hash(bool))</strong></code></td></tr>
      <tr>
        <th>Valor por defecto</th>
        <td><pre class="table-pre"><code class="language-php">'allows' => array('' => true)</code></pre></td>
      </tr>
    </table>

    <p>
      Hash que indica que acciones y por cuales request methods están permitidas.
    </p>

    <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/allows.controllers.conf.php') :)</code></pre>

  </div>

  <div>
    <h3 id="property-services">Formato de respuesta de los webservices</h3>

    <table class="table striped small text-left">
      <tr><th>Propiedad</th><td><code><strong>servicesFormat</strong></code></td></tr>
      <tr><th>Tipo</th><td><code><strong>string</strong></code></td></tr>
      <tr>
        <th>Valor por defecto</th>
        <td><pre class="table-pre"><code class="language-php">'servicesFormat' => 'json'</code></pre></td>
      </tr>
    </table>

    <p>
      Indica el formato de respuesta de los servicios. Puede ser:
    </p>

    <ul>
      <li><code><strong>'json'</strong></code> para codificar la respuesta con <code><strong>json_encode</strong></code></li>
      <li><code><strong>'txt'</strong></code> para codificar la respuesta con <code><strong>var_export</strong></code></li>
      <li>Cualquier otro valor hace que la respuesta se codifique con <code><strong>print_r</strong></code></li>
    </ul>

  </div>

  <div>
    <h3 id="property-filters">Filtros</h3>

    <table class="table striped small text-left">
      <tr><th>Propiedad</th><td><code><strong>filters</strong></code></td></tr>
      <tr><th>Tipo</th><td><code><strong>hash(arrays)</strong></code></td></tr>
      <tr>
        <th>Valor por defecto</th>
        <td><pre class="table-pre"><code class="language-php">'filters' => array()</code></pre></td>
      </tr>
    </table>

    <p>
      Definiciones de los filtros. Los filtros pueden ser configurados para ejecutarse antes y/o después de ciertas acciones y ciertos request methods. Tienen el objetivo de validar condiciones antes de la ejecución de una acción o realizar tareas antes o después de las mismas.
    </p>

    <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/filters.controllers.conf.php') :)</code></pre>

    <p>
      En el caso un filtro ejecutado antes de la acción devuela <code><strong>false</strong></code> o devuelva una instancia de <code><strong>AmResponse</strong></code>, indica que la acción no debe ejecutarse.
    </p>

    <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/filters.Foo.php') :)</code></pre>

  </div>

  <div>
    <h3 id="property-headers">Cabeceras de respuesta</h3>

    <table class="table striped small text-left">
      <tr><th>Propiedad</th><td><code><strong>headers</strong></code></td></tr>
      <tr><th>Tipo</th><td><code><strong>array(string)|hash(string)</strong></code></td></tr>
      <tr>
        <th>Valor por defecto</th>
        <td><pre class="table-pre"><code class="language-php">'headers' => array()</code></pre></td>
      </tr>
    </table>

    <p>
      Listado de cabeceras a incluir en la respuesta.
    </p>

    <p>
      Adicionalmente se puede se puede manejar las cabeceras con los métodos <code><strong>AmController:addHeader</strong></code> y <code><strong>AmControllers::removeHeader</strong></code>:
    </p>

    <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/headers.php') :)</code></pre>

  </div>

</div>

<div>
  <h2 id="route-params">Recibir parámetros de la ruta</h2>
  <p>
    Todos los métodos correspondientes a las acciones y filtros llamados durante la ejecución de una acción reciben como argumentos los parámetros configurados en la ruta y obtenidos de la petición HTTP.
  </p>

  <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/params.routing.php') :)</code></pre>

  <div class="row divide-section">
    <pre class="col s5 table-pre"><code class="language-php">(:= getCodeFile('controllers/params.controllers.php') :)</code></pre>
    <pre class="col s7 table-pre"><code class="language-php">(:= getCodeFile('controllers/params.Foo.php') :)</code></pre>
  </div>

</div>

<div>
  <h2 id="rendering-views">Renderizado de vistas</h2>
  <p>
    Por defecto, al terminar ejecutar una acción se renderiza una vista con el mismo nombre de la acción y extensión <code><strong>.php</strong></code>, la cual es buscada dentro de los directorios de vistas del controlador. En el caso de que no exista simplemente no se realiza acción alguna. La vista tampoco es renderizada en el caso de que se devuelva un array o la instancia de un objeto.
  </p>

  <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/render.Foo.php') :)</code></pre>
  
  <p>
    Para pasar variables a la vista se asignan propiedades al controlador
  </p>

  <div class="row divide-section">
    <pre class="col s6"><code class="language-php">(:= getCodeFile('controllers/render.action.Foo.php') :)</code></pre>
    <pre class="col s6"><code class="language-php">(:= getCodeFile('controllers/render.view.bar.php') :)</code></pre>
  </div>

  <p>
    Para cambiar la vista que se renderizará por la de otra del mismo controlador se utiliza el método <code><strong>AmController::setView</strong></code>:
  </p>

  <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/render.setView.php') :)</code></pre>

  <p>
    Cambiar la vista que se renderizará por otra en de la aplicación se utiliza el método <code><strong>AmController::setRender</strong></code>:
  </p>

  <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/render.setRender.php') :)</code></pre>

  <p>
    Para renderizar la una vista y terminar la acción se retorna lo devuelto por el método <code><strong>AmController:view</strong></code>
  </p>

  <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/render.view.php') :)</code></pre>

</div>

<div>
  <h2 id="response-types">Tipos de respuestas</h2>
  
  <p>
    Los controladores por si solos son un tipo de respuesta, es por esto que heredan de <code><strong>AmResponse</strong></code>. Sin embargo existen otros tipos de respuestas, las cuales se puede utilizar como retorno tanto de los filtros como las acciones del controlador. Las diferentes respuestas que puede darse son:
  </p>

  <div>
    <h3 id="response-template">Responder con una vista</h3>
    <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/response.template.php') :)</code></pre>
  </div>

  <div>
    <h3 id="response-go">Responder con una redirección</h3>
    <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/response.go.php') :)</code></pre>
  </div>

  <div>
    <h3 id="response-file">Responder con un archivo</h3>
    <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/response.file.php') :)</code></pre>
  </div>

  <div>
    <h3 id="response-error">Responder con un error</h3>
    <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/response.error.php') :)</code></pre>
  </div>

  <div>
    <h3 id="response-services">Responder como un webservices</h3>
    <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/response.services.php') :)</code></pre>
  </div>

</div>