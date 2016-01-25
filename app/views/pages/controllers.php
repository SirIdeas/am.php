(:: parent:views/base.php :)
(:: set:pageTitle='Controladores' :)

<div id="pageTitle" class="primary bg-d1 box-shadow-2">
  <div class="content inner spyscroll" data-neq="0" data-class="dispel">
    <h1>(:= $pageTitle :)</h1>
  </div>
</div>

<div class="content inner">
  
  <p>
    Los controladores son manejados por defecto por la extensión <code><strong>AmController</strong></code> la cual es una extensión de <code><strong>AmResponse</strong></code>. Esta permite declarar controladores,
    los cuales tienen acciones que se puede enlazar a las rutas, se le pueden asignar filtros y que renderizan automaticamente vistas.
  </p>

  <div>
    <h2>Rutas</h2>
    
    <p>
      Configurar una ruta a apuntar a una acción de un controlador:
    </p>

    <pre><code class="language-php">(:= getCodeFile('controllers/routing.conf.php') :)</code></pre>

  </div>

  <div>
    <h2>Configuración</h2>

    <p>
      Las configuración de los controladores es tomada de la propiedad de aplicación <code><strong>controllers</strong></code>. Esta es un hash donde cada clave representa el nombre de un controlador y el valor su configuración.
    </p>

    <div class="code-row">
      <table>
        <tr>
          <td class="s6">
            <pre><code class="language-php">(:= getCodeFile('controllers/controllers.conf.php') :)</code></pre>
            <div></div>
          </td>
          <td class="s6">
            <pre><code class="language-php">(:= getCodeFile('controllers/am.conf.php') :)</code></pre>
            <div></div>
          </td>
        </tr>
      </table>
    </div>

  </div>

  <div>
    <h2>Consideraciones</h2>
    <ul>
      <li>
        Por defecto el directorio raíz de los controladores es <code><strong>/app/controllers/</strong></code>.
      </li>
      <li>
        Las clases de los controladores deben extender de <code><strong>AmController</strong></code> o de cualquier otro controlador.
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

    <h2>Propiedades</h2>
    
    <p>
      Las propiedades configurables son:
    </p>
  
    <div>
      
      <h3>Nombre del controlador</h3>

      <table class="table striped small text-left">
        <tr><th>Propiedad</th><td><code><strong>name</strong></code></td></tr>
        <tr><th>Tipo</th><td><code><strong>string</strong></code></td></tr>
        <tr>
          <th>Valor por defecto</th>
          <td><pre class="table-pre"><code class="language-php">'name' => null  </code></pre></td>
        </tr>
      </table>

      <p>
        Define el nombre de la clases del controlador y el nombre de archivo que se incluirá. Si este no es indicado se tomará como nombre como haya sido mencionado en la ruta. En el caso de que no exista una clase con el nombre del controlador después de incluir el archivo correspondiente, el nombre del controlador pasará a ser el mismo que el padre si es que posee este último.
      </p>

      <div class="code-row">
        <table>
          <tr>
            <td class="s6">
              <pre><code class="language-php">(:= getCodeFile('controllers/name.routing.conf.php') :)</code></pre>
              <div></div>
            </td>
            <td class="s6">
              <pre><code class="language-php">(:= getCodeFile('controllers/name.controllers.conf.php') :)</code></pre>
              <div></div>
            </td>
          </tr>
        </table>
      </div>

      <div class="code-row">
        <table>
          <tr>
            <td class="s6">
              <pre><code class="language-php">(:= getCodeFile('controllers/name.Foo.php') :)</code></pre>
              <div></div>
            </td>
            <td class="s6">
              <pre><code class="language-php">(:= getCodeFile('controllers/name.BarCtrl.php') :)</code></pre>
              <div></div>
            </td>
          </tr>
        </table>
      </div>

      <div>
        <small>En el caso del controlador <code><strong>Foo</strong></code> no tiene configuración, sin embargo por defecto los controladores son buscados en la carpeta <code><strong>/app/controllers/</strong></code>. Para el controlador <code><strong>Bar</strong></code> se define el nombre como <code><strong>BarCtrl</strong></code>. Por último, el controlador <code><strong>Baz</strong></code> no posee configuración, ni archivo de declaración y tampoco fue declarada previamente la clase del controlador por lo que las rutas que hacen referencia a este generarán un error 404.</small>
      </div>

    </div>

    <div>
      <h3>Directorio raíz</h3>

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
      <h3>Controlador padre</h3>

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

      <div class="code-row">
        <table>
          <tr>
            <td class="s6">
              <pre><code class="language-php">(:= getCodeFile('controllers/parent.controllers.conf.php') :)</code></pre>
              <div></div>
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
        <small>El controlador <code><strong>Foo</strong></code> se ubica en la carpeta <code><strong>/app/ctrls/</strong></code>. El controlador <code><strong>Bar</strong></code> hereda la configuración de <code><strong>Foo</strong></code> y su comportamiento (por la herencia en la clase), sin embargo se ubica en la carpeta <code><strong>/app/ctrls/bar/</strong></code>. El controlador <code><strong>Baz</strong></code> por su parte hereda solo la configuración de <code><strong>Foo</strong></code>, debido a que la clase hereda de <code><strong>AmController</strong></code>, pese a esto el controlador <code><strong>Foo</strong></code> tambien es cargado al llamar al controlador <code><strong>Baz</strong></code>. Por último el controlador <code><strong>Qux</strong></code> no posee una clase, pero, sus acciones son manejadas a través de una instancia del controlador <code><strong>Bar</strong></code> del cual hereda.
        </small>
      </div>

    </div>

    <div>
      <h3>Directorio principal de vistas</h3>

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
      <h3>Directorios secundarios de vistas</h3>

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
      <h3>Prefijos</h3>

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
      <h3>Acciones permitidas</h3>

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
      <h3>Formato de respuesta de los web services</h3>

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
      <h3>Filtros</h3>

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

  </div>

</div>