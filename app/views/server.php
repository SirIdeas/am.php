(: parent:'views/_docs.php'
(: $pageTitle = 'Configuración del servidor'
(: $subMenuItem = 'routes'

<p>
  El servidor web debe ser configurado para tomar el directorio <code><strong>/public/</strong></code> como directorio base de la aplicación.
</p>

<div>
  <h2 id="apache">Apache</h2>
  <p>
    En el caso de apache la configuración se puede realizar en el archivo <code><strong>.htaccess</strong></code> del directorio <code><strong>/public/</strong></code> o en la configuración de Apache correspondiente a esta carpeta. La configuración sería la siguiente
  </p>
  <pre><code class="language-apacheconf">(:= getCodeFile('configuration/.htaccess') :)</code></pre>
  <p>
    <strong>Nota:</strong> Para el correcto funcinamiento con Apache se requiere tener activado el módulo <code><strong>mod_rewrite</strong></code>.
  </p>

  <h2 id="nginx">Nginx</h2>
  <p>
    Para configurar con Nginx se debe agregar crear un server block para la aplicación o agregar el bloque <code><strong>location</strong></code> al server block adecuado. La configuración sería la siguiente:
  </p>
  <pre><code class="language-nginx">(:= getCodeFile('configuration/serverblock.conf') :)</code></pre>

</div>
