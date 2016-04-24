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
  public static function encode($data, $prepare = true){

    // Preparar la data si es necesario
    if($prepare)
      $data = self::prepare($data);
    
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
