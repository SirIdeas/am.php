(:: parent:views/docs.php :)
(:: set:pageTitle='Modelos' :)

<p>
  Los modelos son abstracciones de la información manejada por lo general gestionada a través de un Sistema Manejador de Base de datos (SMDB). Los modelos permiten buscar agregar, buscar, actualizar y eliminar registros de las tablas de las base de datos configuradas. También permite la realización
  de consulta complejas de una forma sencilla y entendible, creación e eliminación de tablas, vistas y base de datos, entre otros. Todas estas acciones son logradas a través del Mapeo Objeto-Relación (ORM por sus siglas en inglés) propio de Amathista.
</p>

<div>
  <h2>Configuración</h2>

  <p>
    La utilización del ORM comienza con la configuración de las conexiones a las BDs mendiante la propiedad de aplicación <code><strong>schemes</strong></code> la cual es un hash donde cada clave representa el nombre de una conexión y su valor la confuiguración de conexión.
  </p>

  <div class="code-row">
    <table>
      <tr>
        <td class="s6">
          <pre><code class="language-php">(:= getCodeFile('models/models.conf.php') :)</code></pre>
          <div></div>
        </td>
        <td class="s6">
          <pre><code class="language-php">(:= getCodeFile('models/am.conf.php') :)</code></pre>
          <div></div>
        </td>
      </tr>
    </table>
  </div>

</div>

<div>
  <h2>Propiedades</h2>

  <p>
    Las propiedades para configurar la conexión son:
  </p>

  <table class="table striped">
    <thead>
      <tr>
        <th>Propiedad</th>
        <th>Tipo</th>
        <th>Descripción</th>
        <th>Ejemplo</th>
      </tr>
    </thead>
    <tbody class="text-left">
      <tr>
        <td><code><strong>driver</strong></code></td>
        <td><code><strong>string</strong></code></td>
        <td>
          Driver con el que se conectará a la base de datos. Actualmente solo se cuenta con conexión a MySQL.
        </td>
        <td><pre class="table-pre"><code class="language-php">'driver' => 'mysql'</code></pre></td>
      </tr>
      <tr>
        <td><code><strong>database</strong></code></td>
        <td><code><strong>string</strong></code></td>
        <td>
          Nombre de la base de datos a la que se conectará.
        </td>
        <td><pre class="table-pre"><code class="language-php">'database' => 'foo'</code></pre></td>
      </tr>
      <tr>
        <td><code><strong>server</strong></code></td>
        <td><code><strong>string</strong></code></td>
        <td>
          Nombre o dirección del servidor de base de datos al que se conetará.
        </td>
        <td><pre class="table-pre"><code class="language-php">'server' => 'localhost'</code></pre></td>
      </tr>
      <tr>
        <td><code><strong>port</strong></code></td>
        <td><code><strong>int</strong></code></td>
        <td>
          Número del puerto para la conexión.
        </td>
        <td><pre class="table-pre"><code class="language-php">'port' => 3306</code></pre></td>
      </tr>
      <tr>
        <td><code><strong>user</strong></code></td>
        <td><code><strong>string</strong></code></td>
        <td>
          Usuario para la conexión.
        </td>
        <td><pre class="table-pre"><code class="language-php">'user' => 'bar'</code></pre></td>
      </tr>
      <tr>
        <td><code><strong>pass</strong></code></td>
        <td><code><strong>string</strong></code></td>
        <td>
          Password del usario para la conexión.
        </td>
        <td><pre class="table-pre"><code class="language-php">'pass' => 'passsword'</code></pre></td>
      </tr>
      <tr>
        <td><code><strong>charset</strong></code></td>
        <td><code><strong>string</strong></code></td>
        <td>
          Set de caracteres de la basa de datos.
        </td>
        <td><pre class="table-pre"><code class="language-php">'charset' => 'utf8'</code></pre></td>
      </tr>
      <tr>
        <td><code><strong>collation</strong></code></td>
        <td><code><strong>string</strong></code></td>
        <td>
          Reglas de caracteres de la basa de datos.
        </td>
        <td><pre class="table-pre"><code class="language-php">'collation' => 'utf8_unicode_ci'</code></pre></td>
      </tr>
    </tbody>
  </table>

</div>