(:: parent:views/base.php :)
(:: set:pageTitle='Comenzando' :)

<div id="pageTitle" class="primary bg-d1 box-shadow-2">
  <div class="content inner spyscroll" data-neq="0" data-class="dispel">
    <h1>(:= $pageTitle :)</h1>
  </div>
</div>
<div class="content inner">
  
  <div>
    <h2>Cómo funciona</h2>
    <p>
      Amathista es un manejador de eventos capaz incorporar módulos de software extras (extensiones) para ampliar su funcionalidad.
    </p>
    <p>
      Su configuración inicial por defecto permite ser utilizado como marco de trabajo (framework) para el desarrollo aplicaciones web escalables. Sin embargo, su núcleo y extensiones pueden ser utlizado como módulos secundarios o de apoyo otros frameworks.
    </p>
  </div>

  <div>
    <h2>Eventos básicos</h2>
    <p>
      Los eventos en Amathista son unidos a los callbacks mediante el método <code><strong>Am::on</strong></code> y disparados mediante el método <code><strong>Am::ring</strong></code>. Los eventos básicos son:
    </p>
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
          <td>Evalucación de ruta</td>
        </tr>
        <tr>
          <td><code><strong>route.addPreProcessor</strong></code></td>
          <td>Agregar un Pre-callback</td>
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
          <td>Responde con el renderizado de una vista</td>
        </tr>
        <tr>
          <td><code><strong>response.go</strong></code></td>
          <td>Responder con la redirección</td>
        </tr>
        <tr>
          <td><code><strong>response.e404</strong></code></td>
          <td>Reponse con un error 404</td>
        </tr>
        <tr>
          <td><code><strong>response.e403</strong></code></td>
          <td>Reponse con un error 403</td>
        </tr>
        <tr>
          <td><code><strong>response.controller</strong></code></td>
          <td>Responder con la acción de un controlador</td>
        </tr>
        <tr>
          <td><code><strong>render.template</strong></code></td>
          <td>Renderizar vista</td>
        </tr>
      </tbody>
    </table>

  </div>

  <div>
    <h2>Archivo de configuración principal <small>(<code>/app/am.conf.php</code>)</small></h2>
    <p>
      Contienen la inicialización de las propiedades de la aplicación, tales como extensiones a cargar, archivos inciales, configuraciones de los módulos a reescribir y variables de entorno entre otros. Cada item de este archivo de configuración representa un propiedad de la aplicación.
    </p>
    <p>
      Cada propiedad de aplicación es extendería con un archivo de configuración con el mismo nombre ubicado en el mismo directorio por medio de una función de mezcla preconfigurada cuando se intente acceder a esta propiedad. Entonces por ejemplo con el siguiente archivo de configuración principal:
    </p>
    <pre><code class="language-php">(:= getCodeFile('get-started/am.conf.php') :)</code></pre>
    <p>
      las propiedad <code><strong>env</strong></code> se extenderían con el archivo <code><strong>env.conf.php</strong></code> y la propiedad <code><strong>requires</strong></code> con <code><strong>requires.conf.php</strong></code> del directorio <code><strong>/app/</strong></code>.
    </p>
    <div>
      <h3>Propiedades básicas</h3>
      
      El núcleo de Amathista utiliza las siguientes propieades:
      <table class="table striped">
        <thead>
          <tr>
            <th>Propiedad</th>
            <th>Tipo</th>
            <th>Descripción</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><code><strong>errorReporting</strong></code></td>
            <td><code><strong>int</strong></code></td>
            <td>Nivel de errores a reportar. Utiliza la función <a href="http://php.net/manual/es/function.error-reporting.php" target="blank"><code><strong>error_reporting</strong></code></a></td>
          </tr>
          <tr>
            <td><code><strong>requires</strong></code></td>
            <td><code><strong>array</strong></code></td>
            <td>Listado de extensiones a incluir.</td>
          </tr>
          <tr>
            <td><code><strong>files</strong></code></td>
            <td><code><strong>array</strong></code></td>
            <td>Listado de rutas de archivos que se incluyen automáticamente, después de incluir las extensiones.</td>
          </tr>
          <tr>
            <td><code><strong>env</strong></code></td>
            <td><code><strong>hash</strong></code></td>
            <td>Listado de pares variable=>valor del entorno global.</td>
          </tr>
          <tr>
            <td><code><strong>tasks</strong></code></td>
            <td><code><strong>hash</strong></code></td>
            <td>Listado de pares tarea=>configuración de las posibles tareas que se pueden ejecutar.</td>
          </tr>
        </tbody>
      </table>

      <p>
        Las propiedades que se pueden inicializar en el archivo de configuración principal pueden ser ampliadas por cada extensión inclída. Por ejemplo la extensión <code><strong>AmRoute</strong></code> requiere propiedad <code><strong>routing</strong></code>.
      </p>

    </div>
  </div>

  <div>
    <h2>Archivo de inicio principal<small> (<code>/app/am.init.php</code>)</small></h2>
    <p>
      Archivo PHP que se ejecuta luego de incluir las archivos iniciales indicados en la propiedad <code><strong>files</strong></code>. Su fin es la declaración de clases, funciones, constantes y variables globales entre otros.
    </p>
  </div>

  <div>
  </div>

</div>