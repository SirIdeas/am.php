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
    <h3>Declaración básica</h3>
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

    <div>
      <h4>Tomar a consideración</h4>
      <ul>
        <li>
          Por defecto el directorio raíz de los controladores es <code><strong>/app/controllers/</strong></code>.
        </li>
        <li>
          Todos los controladores deben extender de <code><strong>AmController</strong></code> o de cualquier otro controlador.
        </li>
        <li>
          Los nombres de clase de los controladores deben tener el sufijo en '<code><strong>Controller</strong></code>'.
        </li>
        <li>
          Los nombres de archivos de declaración de los controladores debe ser el nombre de clase con el extensión '<code><strong>.class.php</strong></code>'.
        </li>
        <li>
          Por defecto las acciones de los controladores están representadas por sus método con prefijo <code><strong>action_</strong></code>.
        </li>
        <li>
          Por defecto el directorio de vistas de los controladores es la carpeta <code><strong>views</strong></code> dentro de la carpeta raíz del controlador.
        </li>
        <li>
          Las acciones renderizan automáticamente la vista con el mismo que la acción y extensión <code><strong>.view.php</strong></code>.
        </li>
      </ul>
    </div>
  
  </div>

  <div>
    <h3>Configuración</h3>

    <p>
      <code><strong>AmController</strong></code> obtiene las configuraciones de los controladores de la propiedad de aplicación <code><strong>controllers</strong></code>. Esta es un array asociativo donde cada item representa la configuración del controlador con nombre igual al key del item.
    </p>

    <pre><code class="language-php">(:= getCodeFile('/controllers/route.php') :)</code></pre>
    
    <p>
      
    </p>

  </div>


</div>