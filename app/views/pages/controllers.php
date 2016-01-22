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
    <h3>Configuración</h3>

    <p>
      <code><strong>AmController</strong></code> obtiene las configuraciones de los controladores de la propiedad de aplicación <code><strong>controllers</strong></code>. Esta es un hash donde cada clave representa el nombre de un controlador y el valor su configuración.
    </p>

    <pre><code class="language-php">(:= getCodeFile('/controllers/controllers.conf.php') :)</code></pre>
    
    <p>
      Los parámetros configurables son:
    </p>
    <table class="table striped">
      <thead>
        <tr>
          <th class="s1">Parámetros</th>
          <th class="s1">Tipo</th>
          <th>Descripción</th>
          <th class="s2">Valor por defecto</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><code><strong>name</strong></code></td>
          <td><code><strong>string</strong></code></td>
          <td>Nombre del controlador. Si no se define parámetro <code><strong>'name'</strong></code> se toma como nombre del controlador la clave de la configuración del controlador. El nombre del controlador define el <i>Si no se indica el nombre del controlador se toma</i> el cual será el nombre del controlador con el sufijo <code><strong>'Controller'</strong></code></td>. Luego de incluir el archivo de definición del controlador si no existe esta clase el nombre de controlador pasará a ser el nombre del controlador padre. 
          <td><pre class="table-pre"><code class="language-php">'name' => 'Am'</code></pre></td>
        </tr>
        <tr>
          <td><pre><code><strong>parent</strong></code></pre></td>
          <td><pre><code><strong>string</strong></code></pre></td>
          <td>
            Nombre del controlador padre. Este controlador es cargado antes de del controlador hijo y la configuración de este controlador es mezclada con la del controlador hijo.
          </td>
          <td><pre class="table-pre"><code class="language-php">'parent' => null</code></pre></td>
        </tr>
        <tr>
          <td><code><strong>root</strong></code></td>
          <td><code><strong>string</strong></code></td>
          <td>
            Directorio raíz del controlador relativo al directorio de la aplicación. Dentro de este directorio se buscará la configuración propia del controlador (archivo <code><strong>am.conf.php</strong></code>) y se incluirá el archivo con dla definición del controlador.
          </td>
          <td><pre class="table-pre"><code class="language-php">'root' => 'controllers'</code></pre></td>
        </tr>
        <tr>
          <td><code><strong>views</strong></code></td>
          <td><code><strong>string</strong></code></td>
          <td>
            Directorio de vistas del controlador relativo al directorio raíz del controlador.
          </td>
          <td><pre class="table-pre"><code class="language-php">'views' => 'views'</code></pre></td>
        </tr>
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
          <td><pre class="table-pre"><code class="language-php">(:= getCodeFile('/controllers/prefixs.php') :)</code></pre></td>
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
    </table>

  </div>

  <div>
    <h3>Consideraciones</h3>
    <ul>
      <li>
        <p>
          Todos los controladores deben ser declarados.
        </p>
      </li>
      <li>
        <p>
          Por defecto el directorio raíz de los controladores es <code><strong>/app/controllers/</strong></code>.
        </p>
      </li>
      <li>
        <p>
          Todos los controladores deben extender de <code><strong>AmController</strong></code> o de cualquier otro controlador.
        </p>
      </li>
      <li>
        <p>
          Los nombres de clase de los controladores deben tener el sufijo en '<code><strong>Controller</strong></code>'.
        </p>
      </li>
      <li>
        <p>
          Los nombres de archivos de declaración de los controladores debe ser el nombre de clase con el extensión '<code><strong>.class.php</strong></code>'.
        </p>
      </li>
      <li>
        <p>
          Por defecto las acciones de los controladores están representadas por sus método con prefijo <code><strong>action_</strong></code>.
        </p>
      </li>
      <li>
        <p>
          Por defecto el directorio de vistas de los controladores es la carpeta <code><strong>views</strong></code> dentro de la carpeta raíz del controlador.
        </p>
      </li>
      <li>
        <p>
          Las acciones renderizan automáticamente la vista con el mismo que la acción y extensión <code><strong>.view.php</strong></code>.
        </p>
      </li>
    </ul>
  </div>
  
  <div>
    <h3>Uso</h3>
    <p>
      Clase del controlador:
    </p>
    <pre><code class="language-php">(:= getCodeFile('/controllers/FooController.class.php') :)</code></pre>
    <p>
      Vista correspondiente a la acción <code><strong>bar</strong></code>
    </p>
    <pre><code class="language-php">(:= getCodeFile('/controllers/bar.view.php') :)</code></pre>
    <p>
      Enlace con la ruta:
    </p>

    <pre><code class="language-php">(:= getCodeFile('/controllers/route.php') :)</code></pre>
  
  </div>

</div>