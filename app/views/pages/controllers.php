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
      

    </div>

    <!-- <table class="table striped small">
      <tbody>
        <tr>
          <td><code><strong>paths</strong></code></td>
          <td><code><strong>array de strings</strong></code></td>
          <td>
            Lista de directorios alternativos ordenadas por prioridad donde se buscarán las vistas en caso de que no exista esta en el directorio de vistas por defecto. En el caso de que el controlador herede de otro controlador se agregan al final de esta lista el directorio de vistas del padre.
          </td>
          <td><pre class="table-pre"><code class="language-php">'paths' => array()</code></pre></td>
        </tr>
        <tr>
          <td><code><strong>prefixs</strong></code></td>
          <td><code><strong>hash de strings</strong></code></td>
          <td>
            Prefijos utilizados para identificar los diferentes métodos de controlador:
            <ul>
              <li>
                <code><strong>filters</strong></code>: Prefijo para los métodos que representan filtros.
              </li>
              <li>
                <code><strong>actions</strong></code>: Prefijo los métodos correspondientes a las acciones sin importar el <i>request method</i> por el que son recibidas las peticiónes HTTP.
              </li>
              <li>
                <code><strong>getActions</strong></code>: Prefijo para los métodos correspondientes a las acciones recividas por <i>request method</i> GET.
              </li>
              <li>
                <code><strong>postActions</strong></code>: Prefijo para los métodos correspondientes a las acciones recividas por <i>request method</i> POST.
              </li>
            </ul>
          </td>
          <td><pre class="table-pre"><code class="language-php">(:= getCodeFile('controllers/prefixs.php') :)</code></pre></td>
        </tr>
        <tr>
          <td><code><strong>allows</strong></code></td>
          <td><code><strong>hash de bools/hashes de bools</strong></code></td>
          <td>
            Hash que indica por cuales request methods está permitido ejecutar cada acción del controlador. <a href="#">Ver más</a>.
          </td>
          <td><pre class="table-pre"><code class="language-php">'allows' => array()</code></pre></td>
        </tr>
        <tr>
          <td><code><strong>servicesFormat</strong></code></td>
          <td><code><strong>string</strong></code></td>
          <td>
            Indica el formato de respuesta de los servicios. Puede ser <code><strong>'json'</strong></code> para codificar la respuesta con <code><strong>json_encode</strong></code> o <code><strong>'txt'</strong></code> par acodificar la respuesta con <code><strong>var_export</strong></code>. Cualquier otro valor hace que la respuesta se codifique con <code><strong>print_r</strong></code>.
          </td>
          <td><pre class="table-pre"><code class="language-php">'servicesFormat' => 'json'</code></pre></td>
        </tr>
        <tr>
          <td><code><strong>filters</strong></code></td>
          <td><code><strong>hash of arrays</strong></code></td>
          <td>
            Definiciones de los filtros. Los filtros pueden ser configurados para ejecutarse antes y/o despues de ciertas acciones. <a href="#">Ver más</a>.
          </td>
          <td><pre class="table-pre"><code class="language-php">'filters' => array()</code></pre></td>
        </tr>
      </tbody>
    </table> -->

  </div>

</div>