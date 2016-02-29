(:: parent:views/docs.php :)
(:: set:pageTitle='Controladores' :)
(:: set:subMenuItem='controllers' :)

<p>
  Los controladores son clases que poseen métodos llamados acciones, las cuales pueden ser asignadas como procesadores de las rutas. Además de esto, los controladores tambien poseen métodos que pueden ser configurados como filtros para las acciones, con el fin de validar su ejecución, realizar tareas antes y despues de la acción. Además de todo esto, cada acción renderiza una vista con el mismo nombre de la acción, a menos que se indique lo controrio.
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

  <div class="divide-section">
    <table>
      <tr>
        <td class="s6">
          <pre><code class="language-php">(:= getCodeFile('controllers/controllers.conf.php') :)</code></pre>
        </td>
        <td class="s6">
          <pre><code class="language-php">(:= getCodeFile('controllers/am.conf.php') :)</code></pre>
        </td>
      </tr>
    </table>
  </div>

</div>

<div>
  <h2 id="considerations">Consideraciones</h2>
  <ul>
    <li>
      Por defecto el directorio raíz de los controladores es <code><strong>/app/controllers/</strong></code>.
    </li>
    <li>
      Las clases de los controladores deben extender de la clase <code><strong>AmController</strong></code> o de cualquier otro controlador.
    </li>
    <li>
      El nombre de la clase del controlador es el mismo nombre del controlador.
    </li>
    <li>
      El nombre del archivo de declaración de un controlador es el nombre de clase con el extensión '<code><strong>.php</strong></code>' y se buscará en el directorio raíz.
    </li>
    <li>
      Por defecto las acciones de los controladores están representadas por los métodos del mismo con el prefijo <code><strong>action_</strong></code>.
    </li>
    <li>
      Por defecto el directorio de vistas de los controladores es la carpeta <code><strong>views</strong></code> dentro de la carpeta raíz del controlador.
    </li>
    <li>
      Las acciones renderizan automáticamente la vista con el mismo que la acción y extensión <code><strong>.php</strong></code>.
    </li>
  </ul>
</div>

<div>

  <h2 id="properties">Propiedades</h2>
  
  <p>
    Las propiedades configurables son:
  </p>

  <div>
    
    <h3 id="property-name">Nombre del controlador</h3>

    <table class="table striped small text-left">
      <tr><th>Propiedad</th><td><code><strong>name</strong></code></td></tr>
      <tr><th>Tipo</th><td><code><strong>string</strong></code></td></tr>
      <tr>
        <th>Valor por defecto</th>
        <td><pre class="table-pre"><code class="language-php">'name' => null</code></pre></td>
      </tr>
    </table>

    <p>
      Define el nombre de la clases del controlador y el nombre de archivo que se incluirá. Si este no es indicado se tomará como nombre como haya sido mencionado en la ruta. En el caso de que no exista una clase con el nombre del controlador después de incluir el archivo correspondiente, el nombre del controlador pasará a ser el mismo que el padre si es que posee este último.
    </p>

    <div class="divide-section">
      <table>
        <tr>
          <td class="s6">
            <pre><code class="language-php">(:= getCodeFile('controllers/name.routing.conf.php') :)</code></pre>
          </td>
          <td class="s6">
            <pre><code class="language-php">(:= getCodeFile('controllers/name.controllers.conf.php') :)</code></pre>
          </td>
        </tr>
      </table>
    </div>

    <div class="divide-section">
      <table>
        <tr>
          <td class="s6">
            <pre><code class="language-php">(:= getCodeFile('controllers/name.Foo.php') :)</code></pre>
          </td>
          <td class="s6">
            <pre><code class="language-php">(:= getCodeFile('controllers/name.BarCtrl.php') :)</code></pre>
          </td>
        </tr>
      </table>
    </div>

    <div>
      <small>Nota: En el caso del controlador <code><strong>Foo</strong></code> no tiene configuración, sin embargo por defecto los controladores son buscados en la carpeta <code><strong>/app/controllers/</strong></code>. Para el controlador <code><strong>Bar</strong></code> se define el nombre como <code><strong>BarCtrl</strong></code>. Por último, el controlador <code><strong>Baz</strong></code> no posee configuración, ni archivo de declaración y tampoco fue declarada previamente la clase del controlador por lo que las rutas que hacen referencia a este generarán un error 404.</small>
    </div>

  </div>

  <div>
    <h3 id="property-root">Directorio raíz</h3>

    <table class="table striped small text-left">
      <tr><th>Propiedad</th><td><code><strong>root</strong></code></td></tr>
      <tr><th>Tipo</th><td><code><strong>string</strong></code></td></tr>
      <tr>
        <th>Valor por defecto</th>
        <td><pre class="table-pre"><code class="language-php">'root' => 'controllers'</code></pre></td>
      </tr>
    </table>

    <p>
      Directorio raíz del controlador relativo al directorio de la aplicación. Dentro de este directorio se busca el archivo de configuración propio (<code><strong>am.conf.php</strong></code>), el archivo de declaración y el directorio de vistas correspondiente al controlador.
    </p>

    <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/root.controllers.conf.php') :)</code></pre>

  </div>

  <div>
    <h3 id="property-parent">Controlador padre</h3>

    <table class="table striped small text-left">
      <tr><th>Propiedad</th><td><code><strong>parent</strong></code></td></tr>
      <tr><th>Tipo</th><td><code><strong>string</strong></code></td></tr>
      <tr>
        <th>Valor por defecto</th>
        <td><pre class="table-pre"><code class="language-php">'parent' => null</code></pre></td>
      </tr>
    </table>

    <p>
      Nombre del controlador padre y del cual se hereda la configuración. Este controlador es cargado antes de cargar el actual. En el caso de que no exista la clase del controlador actual se intentará instancia el controlador padre.
    </p>

    <div class="divide-section">
      <table>
        <tr>
          <td class="s6">
            <pre><code class="language-php">(:= getCodeFile('controllers/parent.controllers.conf.php') :)</code></pre>
          </td>
          <td class="s6">
            <pre class="mb5"><code class="language-php">(:= getCodeFile('controllers/parent.Foo.php') :)</code></pre>
            <pre class="mb5"><code class="language-php">(:= getCodeFile('controllers/parent.Bar.php') :)</code></pre>
            <pre><code class="language-php">(:= getCodeFile('controllers/parent.Baz.php') :)</code></pre>
          </td>
        </tr>
      </table>
    </div>
    <div>
      <small>Nota: El controlador <code><strong>Foo</strong></code> se ubica en la carpeta <code><strong>/app/ctrls/</strong></code>. El controlador <code><strong>Bar</strong></code> hereda la configuración de <code><strong>Foo</strong></code> y su comportamiento (por la herencia en la clase), sin embargo se ubica en la carpeta <code><strong>/app/ctrls/bar/</strong></code>. El controlador <code><strong>Baz</strong></code> por su parte hereda solo la configuración de <code><strong>Foo</strong></code>, debido a que la clase hereda de <code><strong>AmController</strong></code>, pese a esto el controlador <code><strong>Foo</strong></code> tambien es cargado al llamar al controlador <code><strong>Baz</strong></code>. Por último el controlador <code><strong>Qux</strong></code> no posee una clase, pero, sus acciones son manejadas a través de una instancia del controlador <code><strong>Bar</strong></code> del cual hereda.
      </small>
    </div>

  </div>

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
      Lista de directorios secundarios ordenados por prioridad donde se buscarán las vistas de las acciones del controlador. A diferencia del directorio <code><strong>views</strong></code> que es relativo al directorio raíz de la controlador, estos directorios deben ser realitivos al directorios raíz de la aplicación. Un controlador con la propiedad <code><strong>parent</strong></code> hereda el directorio <code><strong>views</strong></code> y los paths del padre.
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
      Prefijos utilizados para identificar cada tipo de métodos del controlador. Básicamente posee dos tiupos: <strong>acciones</strong> que puede ser solicitadas por los diferentes requests methods y <strong>filtros</strong> que puede ser ejecutados antes y/o después del método de una acción.
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
      Hash que indica que acciones y por cuales request methods están permitidas. Los controladores que heredan la configuración de un padre mazcla el contenido de esta propiedad en el padre con las del hijo, prevaleciendo las del último.
    </p>

    <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/allows.controllers.conf.php') :)</code></pre>

  </div>

  <div>
    <h3 id="property-services">Formato de respuesta de los web services</h3>

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
      Definiciones de los filtros. Los filtros pueden ser configurados para ejecutarse antes y/o después de ciertas acciones y ciertos request methods. Tienen el objetivo de validar condiciones antes de la ejecución de una acción o realizar tareas anteriores o posteriores a una acción.
    </p>

    <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/filters.controllers.conf.php') :)</code></pre>

    <p>
      En el caso un filtro ejecutado antes de la acción devuela <code><strong>false</strong></code> o devuelva una instancia de <code><strong>AmResponse</strong></code>, indica que la acción de debe ejecutarse.
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
      Listado de cabeceras a incluir en la respuesta. Si el controlador posee un controlador padre, el primero hereda las cabeceras del segundo.
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

  <div class="divide-section">
    <table>
      <tr>
        <td class="s5">
          <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/params.controllers.php') :)</code></pre>
        </td>
        <td class="s7">
          <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/params.Foo.php') :)</code></pre>
        </td>
      </tr>
    </table>
  </div>

</div>

<div>
  <h2 id="rendering-views">Renderizado de vistas</h2>
  <p>
    Por defecto, al terminar ejecutar una acción se renderiza una vista con el mismo nombre de la acción y extensión <code><strong>.php</strong></code>, la cual es buscada dentro de los directorios de vistas del controlador. En el caso de que no exista simplemente no se realiza acción alguna. La vista tampoco de renderiza en el caso de que se devuelva un array o la instancia de un objecto.
  </p>

  <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/render.Foo.php') :)</code></pre>
  
  <p>
    Para pasar variables a la vista se asignan propiedades al controlador
  </p>

  <div class="divide-section">
    <table>
      <tr>
        <td class="s6">
          <pre><code class="language-php">(:= getCodeFile('controllers/render.action.Foo.php') :)</code></pre>
        </td>
        <td class="s6">
          <pre><code class="language-php">(:= getCodeFile('controllers/render.view.bar.php') :)</code></pre>
        </td>
      </tr>
    </table>
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
    Para renderizar la una vista y terminar la acción se utiliza el método <code><strong>AmController:view</strong></code>
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
    <h3 id="response-services">Responder como un web services</h3>
    <pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/response.services.php') :)</code></pre>
  </div>

</div>