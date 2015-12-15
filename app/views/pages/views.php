(:: parent:views/base.php :)
(:: set:pageTitle='Vistas' :)

<div id="pageTitle" class="primary bg-d1 box-shadow-2">
  <div class="content inner spyscroll" data-neq="0" data-class="dispel">
    <h1>(:= $pageTitle :)</h1>
  </div>
</div>

<div class="content inner">
  
  <h2>AmView</h2>
  <p>
    Para el renderizado de vistas Amathista usa la extensión <code><strong>AmTpl</strong></code> por defecto. Esta extensión permite incluir vistas una dentro de otras, definir secciones, definir variables, imprimir variables, definir herencia y embeber código PHP a través de <i>directivas</i>.
  </p>

  <div>
    <h3>Código embebido</h3>
    <p>
      Para agregar código PHP propio dentro de la vista se puede de la forma tradicional, o utilizar las etiquetas <code><strong>(<span>:</span></strong></code> y <code><strong><span>:</span>)</strong></code>:
    </p>
    <pre><code class="language-php">(:= getCodeFile('/views/embedded.php') :)</code></pre>
  </div>

  <div>
    <h3>URL raíz de la aplicación</h3>
    <p>
      Adicionalmente, la directiva especial <code><strong>(:/:)</strong></code> es sustituida por la URL raíz de la aplicación (<code><strong>Am::url()</strong></code>):
    </p>
    <pre><code class="language-php">(:= getCodeFile('/views/url.php') :)</code></pre>
  </div>
  
  <div>
    <h3>Directivas</h3>
    <p>
      Las directivas son las primeras sentencias que se ejecutan de una vista. Estas se diferencian el código embebido en que estas se encuentran entre una etiqueta <code><strong>(<span>::</span></strong></code> y <code><strong><span>:</span>)</strong></code>.
    </p>
    <div>
      <h4>Herencia</h4>
      <p>
        Para la herencia se utiliza el método <code><strong>parent</strong></code> el cual define de que vista se hereda; asi mismo la vista vista hiuja es insertada donde la vista padre ejecute el método <code><strong>child</strong></code>:
      </p>
      <div class="row code-row">
        <div class="col s4">
          <pre><code class="language-php">(:= getCodeFile('/views/parent1.php') :)</code></pre>
        </div>
        <div class="col s4">
          <pre><code class="language-php">(:= getCodeFile('/views/parent2.php') :)</code></pre>
        </div>
        <div class="col s4">
          <pre><code class="language-php">(:= getCodeFile('/views/parent3.php') :)</code></pre></div>
      </div>
    </div>

    <div>
      <h4>Vistas anidadas</h4>
      <p>
        Para incluir vistas anidadas se utiliza el método <code><strong>place</strong></code>:
      </p>
      <div class="row code-row">
        <div class="col s4">
          <pre><code class="language-php">(:= getCodeFile('/views/place1.php') :)</code></pre>
        </div>
        <div class="col s4">
          <pre><code class="language-php">(:= getCodeFile('/views/place2.php') :)</code></pre>
        </div>
        <div class="col s4">
          <pre><code class="language-php">(:= getCodeFile('/views/place3.php') :)</code></pre>
        </div>
      </div>
    </div>

    <div>
      <h4>Secciones</h4>
      <p>
        Para definir secciones se utiliza el método <code><strong>section</strong></code> para iniciarla y <code><strong>endSection</strong></code> para cerrarla:
      </p>
      <pre><code class="language-php">(:= getCodeFile('/views/sections.php') :)</code></pre>
      <p>
        Para utilizar secciones se utiliza él método <code><strong>put</strong></code>:
      </p>
      <div class="row code-row">
        <div class="col s6">
          <pre><code class="language-php">(:= getCodeFile('/views/put.php') :)</code></pre>
        </div>
        <div class="col s6">
          <pre><code class="language-php">(:= getCodeFile('/views/put-result.php') :)</code></pre>
        </div>
      </div>
      <p>
        De esta forma, si una sección se vuelve a definir su contenido es sustituído por el nuevo. Se se desea conservar el contenido anterior se debe colocar el signo <code><strong>+</strong></code> antes despúes del nombre de la sección para indicar si el nuevo contenido se agrega al final o al principio.
      </p>
      <div class="row code-row">
        <div class="col s6">
          <pre><code class="language-php">(:= getCodeFile('/views/section-concat.php') :)</code></pre>
        </div>
        <div class="col s6">
          <pre><code class="language-php">(:= getCodeFile('/views/section-concat-result.php') :)</code></pre>
        </div>
      </div>

    </div>

    <div>
      <h4>Variables</h4>
      <p>
        Para asignar valores a variables se utiliza el método <code><strong>set</strong></code>:
      </p>
      <pre><code class="language-php">(:= getCodeFile('/views/set.php') :)</code></pre>
      <p>
        Los valores asignados con <code><strong>set</strong></code> reescriben a los asignados en la vistas padres o agregadas anteriormente.
      </p>
    </div>
    
  </div>

</div>