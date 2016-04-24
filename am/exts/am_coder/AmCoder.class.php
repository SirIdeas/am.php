<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Codificador/decodificador de arrays.
 */
class AmCoder{

  /**
   * Decodifica un archivo de configuracion con el mismo formato.
   * La decodificación se basa solo en obtener lo retornado en el archivo.
   * @param  string      $file    Ruta del archivo a decodificar.
   * @param  mixed       $default Valor por defecto a devolver si el archivo no
   *                              existe.
   * @return mixed/array          Retorno del archivo, sino el archivo no existe
   *                              devuelve el valor por defecto.
   */
  public static function decode($file, $default = array()){

    // Si el archivo exite retornar lo que devuelva el mismo
    if(is_file($file))
      return require $file;

    // Si no existe el archivo retornan el valor por defecto
    return $default;

  }

  /**
   * Leer el archivo.
   * @param  string      $file    Ruta del archivo a decodificar.
   * @param  mixed       $default Valor por defecto a devolver si el archivo no
   *                              existe.
   * @return mixed/array          Retorno del archivo, sino el archivo no existe
   *                              devuelve el valor por defecto.
   */
  public static function read($file, $default = array()) {

    // Si existe decodificar el contenido
    if(self::exists($file))
      return self::decode($file, $default);

    // Si no existe el archivo retornan el valor por defecto
    return $default;

  }

  /**
   * Indica si el archivo existe.
   * @param  string $file Ruta del archivo que desean consultar.
   * @return bool         Si el archivo existe.
   */
  public static function exists($file){
    return is_file($file);
  }

  /**
   * Escribir contenido del archivo.
   * @param string $file    Ruta del archivo que desean escribir.
   * @param array  $data    Array con la información a escribir.
   * @param bool   $prepare Indica si se preparará o no la información.
   */
  public static function write($file, array $data, $prepare = true) {

    // Preparar la data si es necesario
    if($prepare)
      $data = self::prepare($data);

    // Crear directorio donde se ubicará el archivo
    Am::mkdir(dirname($file));

    // Si el archivo no existe se crea el archivo
    file_put_contents($file, self::encode($data));

  }

  /**
   * Guarda una configuración en archivo.
   * @param  string $path Archivo donde se guardará la configuración.
   * @param  hash   $conf Hash con la configuración q se guadará.
   * @param  bool   $rw   Indica si el archivo se debe sobreescribir en el caso
   *                      de que no exista.
   * @return hash         Hash de propiedades del modelo.
   */
  public static function generate($path, $conf, $rw = true){

    if(!is_file($path) || $rw){
      self::write($path, $conf);
      return true;
    }
    return false;
  }

  /**
   * Preparación de la información para escribir en el archivo.
   * Consiste en crear array anidados en aquellas posiciones cuya key tenga el
   * caractere _
   * @param  array $data Array con la información a preparar.
   * @return array       Data preparada.
   */
  public static function prepare(array $data){

    $ret = array();
    foreach($data as  $key => $value){
      $key = explode('_', $key);
      self::_prepare($ret, $key, $value);
    }

    return $ret;

  }
  
  /**
   * Función auxiliar para preparar la data.
   * @param  array  $data Array con la información a preparar.
   * @param  string $path Lista de keys que indica la ruta donde se encuentra
   *                      actualmente dentro del array.
   * @return int
   */
  private static function _prepare(array &$data, array $path, $value){

    if(empty($path)){
      if(count($data)>1)
        return 1;
      if(isset($data['_']) || empty($data))
        return 2;
    }

    $key = array_shift($path);

    if(!isset($data[$key])){
      $data[$key] = array();
    }else if(!is_array($data[$key])){
      $data[$key] = array(
        '_' => $data[$key]
      );
    }

    switch (self::_prepare($data[$key], $path, $value)){
      case 1:
        $data[$key]['_'] = $value;
        break;
      case 2:
        $data[$key] = $value;
        break;
    }

    return 0;

  }

  /**
   * Método que codifica la data.
   * @param  array  $data Array a codificar.
   * @return string       Resultado de la codificación.
   */
  public static function encode($data){
    
    $str = var_export($data, true);
    $lines = explode("\n", $str);
  
    // improves result encoding.
    $prev = false;
    foreach($lines as $i => $str){
      if(trim($str) == '),' && $prev){
        $lines[$i-2] .= '),';
        $lines[$i] = false;
      }elseif(trim($str) == 'array ('){
        $lines[$i] = str_replace('array (', 'array(', $str);
        if($i > 0){
          $lines[$i-1] .= trim($lines[$i]);
          $lines[$i] = false;
        }
        $prev = true;
      }else{
        $prev = false;
      }
    }

    $lines = array_filter($lines);
    $lines = implode("\n", $lines);

    return "<?php\n\nreturn {$lines};";

  }
  
}
