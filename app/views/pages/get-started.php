(:: parent:views/docs.php :)
(:: set:pageTitle='Comenzando' :)
(:: set:subMenuItem='get-started' :)

<div>
  <h2 id="how-works">Cómo funciona</h2>
  <p>
    Amathista es un manejador de eventos capaz incorporar módulos de software extras (extensiones) para ampliar su funcionalidad.
  </p>
  <p>
    Su configuración inicial por defecto permite ser utilizado como marco de trabajo (framework) para el desarrollo aplicaciones web escalables. Sin embargo, su núcleo y extensiones pueden ser utlizado como módulos secundarios o de apoyo a otros frameworks.
  </p>
</div>

<div>
  <h2 id="basics-actions">Acciones básicas</h2>
  <p>
    Las acciones en Amathista son unidas a callbacks mediante el método <code><strong>Am::on</strong></code> y disparados mediante el método <code><strong>Am::emit</strong></code>. Las acciones permiten asignar funcionalidades a los callbacks de las extensiones. En otros casos son utilizados para reportar eventos en la aplicación. Las acciones básicos son:
  </p>
  <div class="table-responsive">
    <table class="table striped">
      <thead>
        <tr>
          <th>Evento</th>
          <th>Descripción</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><code><strong>route.evaluate</strong></code></td>
          <td>Evaluar una ruta</td>
        </tr>
        <tr>
          <td><code><strong>route.addPreProcessor</strong></code></td>
          <td>Agregar un pre-callback</td>
        </tr>
        <tr>
          <td><code><strong>route.addDispatcher</strong></code></td>
          <td>Agregar un despachador de ruta</td>
        </tr>
        <tr>
          <td><code><strong>response.file</strong></code></td>
          <td>Responder con un archivo</td>
        </tr>
        <tr>
          <td><code><strong>response.call</strong></code></td>
          <td>Responder con la llamada de una función o método</td>
        </tr>
        <tr>
          <td><code><strong>response.template</strong></code></td>
          <td>Responder con el renderizado de una vista</td>
        </tr>
        <tr>
          <td><code><strong>response.go</strong></code></td>
          <td>Responder con la redirección</td>
        </tr>
        <tr>
          <td><code><strong>response.e404</strong></code></td>
          <td>Responder con un error 404</td>
        </tr>
        <tr>
          <td><code><strong>response.e403</strong></code></td>
          <td>Responder con un error 403</td>
        </tr>
        <tr>
          <td><code><strong>response.controller</strong></code></td>
          <td>Responder con la acción de un controlador</td>
        </tr>
        <tr>
          <td><code><strong>session.get</strong></code></td>
          <td>Obtener una sesión con un determinado identificador</td>
        </tr>
        <tr>
          <td><code><strong>core.loadedPath</strong></code></td>
          <td>Evento que indica que se cargó un nuevo directorio de clases</td>
        </tr>
      </tbody>
    </table>
  </div>
  
</div>

<div>
  <h2 id="main-conf-file">Archivo principal de configuración <small>(<code>/app/am.conf.php</code>)</small></h2>
  <p>
    Contiene la inicialización de las propiedades de la aplicación, tales como extensiones a cargar, archivos iniciales, configuraciones de las extensiones a sobreescribir y variables de entorno entre otros. Cada item de este archivo de configuración representa un propiedad de la aplicación.
  </p>
  <p>
    Cada propiedad de aplicación se extendería con un archivo de configuración con el mismo nombre ubicado en el mismo directorio por medio de una función de mezcla preconfigurada cuando se intente acceder a esta propiedad. Entonces por ejemplo con el siguiente archivo de configuración principal:
  </p>
  <pre><code class="language-php">(:= getCodeFile('get-started/am.conf.php') :)</code></pre>
  <p>
    las propiedad <code><strong>env</strong></code> se extenderían con el archivo <code><strong>env.conf.php</strong></code> y la propiedad <code><strong>requires</strong></code> con <code><strong>requires.conf.php</strong></code> del directorio <code><strong>/app/</strong></code>.
  </p>
  <div>
    <h3 id="basics-properties">Propiedades básicas</h3>
    
    <p>El núcleo de Amathista utiliza las siguientes propieades:</p>

    <div class="table-responsive">
      <table class="table striped">
        <thead>
          <tr>
            <th>Propiedad</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Valor por defecto</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><code><strong>id</strong></code></td>
            <td><code><strong>string</strong></code></td>
            <td>Identificador de la aplicación</td>
            <td><pre class="table-pre"><code class="language-php">'id' => 'am'</code></pre></td>
          </tr>
          <tr>
            <td><code><strong>errorReporting</strong></code></td>
            <td><code><strong>int</strong></code></td>
            <td>Nivel de errores a reportar. Utiliza la función <a href="http://php.net/manual/es/function.error-reporting.php" target="blank"><code><strong>error_reporting</strong></code></a></td>
            <td><pre class="table-pre"><code class="language-php">'errorReporting' => E_ALL ^ E_DEPRECATED</code></pre></td>
          </tr>
          <tr>
            <td><code><strong>consts</strong></code></td>
            <td><code><strong>hash</strong></code></td>
            <td>Listado de pares constante=>valor a definir automaticamente con la funciuón <code>define</code>.</td>
            <td><pre class="table-pre"><code class="language-php">'consts' => array(),</code></pre></td>
          </tr>
          <tr>
            <td><code><strong>env</strong></code></td>
            <td><code><strong>hash</strong></code></td>
            <td>Listado de pares variable=>valor del entorno global.</td>
            <td><pre class="table-pre"><code class="language-php">'env' => array(),</code></pre></td>
          </tr>
          <tr>
            <td><code><strong>requires</strong></code></td>
            <td><code><strong>array</strong></code></td>
            <td>Listado de extensiones a incluir.</td>
            <td><pre class="table-pre"><code class="language-php">'requires' => array(),</code></pre></td>
          </tr>
          <tr>
            <td><code><strong>aliases</strong></code></td>
            <td><code><strong>hash</strong></code></td>
            <td>Listado de pares alias=>extensión para identificar extensiones por nombres cortos.</td>
            <td><pre class="table-pre"><code class="language-php">'requires' => array(),</code></pre></td>
          </tr>
          <tr>
            <td><code><strong>autoload</strong></code></td>
            <td><code><strong>array</strong></code></td>
            <td>
              Listado de directorios donde se buscan las clases (archivos con extensión .class.php) y archivos que se incluyen automáticamente, después de incluir las extensiones.
            </td>
            <td><pre class="table-pre"><code class="language-php">'autoload' => array(),</code></pre></td>
          </tr>
          <tr>
            <td><code><strong>formats</strong></code></td>
            <td><code><strong>hash</strong></code></td>
            <td>Listado de pares nombre=>formato de los formatos de la aplicación. Los formatos deben ser compatibles con los utilizados por la función <a href="http://php.net/manual/es/function.sprintf.php" target="blank"><code><strong>sprintf</strong></code></a>.</td>
            <td><pre class="table-pre"><code class="language-php">'tasks' => array(),</code></pre></td>
          </tr>
          <tr>
            <td><code><strong>tasks</strong></code></td>
            <td><code><strong>hash</strong></code></td>
            <td>Listado de pares tarea=>configuración de las posibles tareas que se pueden ejecutar.</td>
            <td><pre class="table-pre"><code class="language-php">'tasks' => array(),</code></pre></td>
          </tr>
        </tbody>
      </table>
    </div>

    <p>
      Las propiedades que se pueden inicializar en el archivo de configuración principal pueden ser ampliadas por cada extensión incluída. Por ejemplo la extensión <code><strong>AmRoute</strong></code> utilizada para el manejo de las rutas utiliza la propiedad <code><strong>routing</strong></code>.
    </p>

  </div>
</div>

<div>
  <h2 id="main-init-file">Archivo principalde inicio <small> (<code>/app/am.init.php</code>)</small></h2>
  <p>
    Archivo PHP que se ejecuta luego de incluir las archivos iniciales indicados en la propiedad <code><strong>files</strong></code>. Su fin es la declaración de clases, funciones, constantes y variables globales entre otros.
  </p>
</div>