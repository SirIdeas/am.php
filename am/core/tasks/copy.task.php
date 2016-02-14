<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Copia los archivos.
 * @param  string  $origin  De donde se tomarán los archivo.
 * @param  string  $destiny Donde se copiará los archivos.
 * @param  array   $regexs  Listado de regex para selecionar los archivos a
 *                          copiar.
 * @param  boolean $rewrite si se reescribiran los archivos.
 */
function task_copy($origin, $destiny, array $regexs, $rewrite = false, $vervose = false){

  // Obtener el direcotorio real
  $origin = realpath($origin);

  // Si la carpeta de origen no existe se sale de la función;
  if(!$origin)
    return false;

  $msgs = array(
    'files' => array(),
    'copieds' => array(),
    'rewriteds' => array(),
    'ignoreds' => array(),
    'fail_copy' => array(),
    'recycleds' => array(),
    'fail_recycle' => array(),
  );

  // REcorer
  foreach ($regexs as $regex) {

    // Obtener listado de archivos publicas a copiar
    $files = glob($origin.$regex, GLOB_BRACE);

    foreach ($files as $file) {
      $file = substr_replace($file, '', 0, strlen($origin));

      $msgs['files'][] = $file;

      $copied = false;
      $orig = $origin.$file;
      $dest = $destiny.$file;

      // Crear carpeta donde se copiará el archivo
      @mkdir(dirname($dest), 0755, true);

      // Si el archivo no existe se copia
      if(!is_file($dest) && !is_dir($dest)){

        if(@copy($orig, $dest)){
          $msgs['copieds'][] = $file;
        }else{
          $msgs['fail_copy'][] = $file;
        }

      // Si el archivo existe y se pide que se sobreescriba
      }elseif(is_file($dest) && $rewrite){

        // Se envia el archivo a la papelera
        if(Am::sendToTrash($dest)){
          $msgs['recycleds'][] = $file;

          if(@copy($orig, $dest)){
            $msgs['rewriteds'][] = $file;
          }else{
            $msgs['fail_copy'][] = $file;
          }

        }else{
          $msgs['fail_recycle'][] = $file;
        }

      }else{

        $msgs['ignoreds'][] = $file;

      }

    }

  }

  if($vervose)
    echo
     'Copiar '.count($msgs['files'])." archivo(s):\n".
     "\t".count($msgs['copieds'])." agregado(s).\n".
     "\t".count($msgs[$rewrite?'rewriteds':'ignoreds']).($rewrite?
      ' reescrito(s)': ' ignorado(s)').".\n",
     "\t".count($msgs['fail_copy'])." fallaron al copiar.\n".
     "\t".count($msgs['recycleds'])." archivo(s) reciclado(s).\n".
     "\t".count($msgs['fail_recycle'])." archivo(s) fallaron al reciclar(s).\n";

  return $msgs;

}