<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para los campos de las tablas de las BD
 */
class AmField extends AmObject{

  protected static
    
    /**
     * Funciones para obtener el valor segun el tipo de dato
     */
    $parseFuncs = array(

      // Enteros
      'integer'     => 'intval',
      'bit'         => 'strval',

      // Flotantes
      'float'       => 'floatval',

      // Cadenas de caracteres
      'char'        => 'strval',
      'varchar'     => 'strval',
      'text'        => 'strval',

      // Fechas
      'date'        => 'strval',
      'datetime'    => 'strval',
      'timestamp'   => 'strval',
      'time'        => 'strval',
      'year'        => 'strval',
      
    );

  protected

    /**
     * String con el nombre.
     */
    $name = null,

    /**
     * String con el tipo de datos.
     */
    $type = 'text',

    /**
     * Valor por defecto.
     */
    $defaultValue = null,

    /**
     * Bool que indica si es o no parte de la clave primaria de la tabla donde
     * se encuentra.
     */
    $pk = false,

    /**
     * Bool si permite o no valores nulos.
     */
    $allowNull = true,

    /**
     * Entero con el tamaño de campo. Para los tipos varchar indicar el máximo
     * tamaño, para campos del tipo bit y char el tamaño, para los tipos
     * integer y floats indicar los bytes que ocupan.
     */
    $len = null,

    /**
     * String con el charset.
     */
    $charset = null,

    /**
     * String con el Collage.
     */
    $collage = null,

    /**
     * Bool que indica si en un campo numérico (integer o float) tiene signo.
     */
    $unsigned = null,

    /**
     * Bool que indica si es un integer o float indica si se rellenan los
     * espacio con ceros.
     */
    $zerofill = null,

    /**
     * Entero con la precición de enteros de los números float.
     */
    $precision = null,

    /**
     * Entero con la precición de decimales de los números float.
     */
    $scale = null,

    /**
     * Bool que indica si es un campo autoincrementable.
     */
    $autoIncrement = false,

    /**
     * String con configuración extra.
     */
    $extra = null;

  /**
   * Sobre escribir el parámetro para detectar los tipos autoIncrement y
   * cambiarlo adecuadamente.
   * @param hash $params Listado de propiedades
   */
  public function __construct($params = null){

    // Transformar atributos a array
    $params = self::parse($params);

    // Obtener el typo
    $type = itemOr('type', $params);

    // Tipo especial de datos: id, autoIncrement y unsigned
    if(in_array($type, array('id', 'autoIncrement', 'unsigned'))){
      $params['type'] = 'integer';
      $params['unsigned'] = true;
    }
    
    // Tipo especial id y autoIncrement
    if(in_array($type, array('id', 'autoIncrement'))){
      $params['autoIncrement'] = true;
    }

    // Tipo especial id
    if($type == 'id'){
      $params['pk'] = true;
    }

    // LLamar al constructor del padre
    parent::__construct($params);

  }

  /**
   * Devuelve el nombre.
   * @return string Nombre.
   */
  public function getName(){

    return $this->name;
  
  }

  /**
   * Devuelve el tipo.
   * @return string Tipo.
   */
  public function getType(){

    return $this->type;
  
  }

  /**
   * Devuelve el Tamaño.
   * @return int Tamaño.
   */
  public function getLen(){

    return $this->len;
  
  }

  /**
   * Devuelve la precisión de enteros de campos float.
   * @return int Precisión de enteros de campos float.
   */
  public function getPrecision(){

    return $this->precision;
  
  }

  /**
   * Devuelve la precisión de decimales de campos float.
   * @return int Precición de decimales de campos float.
   */
  public function getScale(){

    return $this->scale;
  
  }

  /**
   * Devuelve el valor por defecto.
   * @return mixed Valor por defecto.
   */
  public function getDefaultValue(){

    return $this->defaultValue;
  
  }

  /**
   * Devuelve si permite o no valores null.
   * @return bool Si permite o no valores nullos.
   */
  public function allowNull(){

    return $this->allowNull;
  
  }

  /**
   * Devuelve si es un campo autoincrementable.
   * @return bool Si es un campo autoincrementable.
   */
  public function isAutoIncrement(){

    return $this->autoIncrement;
  
  }

  /**
   * Devuelve si es un campo sin signo.
   * @return bool Si es un campos sin signo.
   */
  public function isUnsigned(){

    return $this->unsigned;
  
  }

  /**
   * Devuelve si se rrellenan con ceros.
   * @return bool Si se rellena con ceros.
   */
  public function isZerofill(){

    return $this->zerofill;
  
  }

  /**
   * Devuelve si es parte de la clave primaria de la tabla a la que pertenece.
   * @return bool Si es parte de la clave primaria.
   */
  public function isPk(){

    return $this->pk;

  }

  /**
   * Devuelve el charset.
   * @return string Charset.
   */
  public function getCharset(){

    return $this->charset;
  
  }

  /**
   * Devuelve el collage.
   * @return string Collage.
   */
  public function getCollage(){

    return $this->collage;
  
  }

  /**
   * Devuelve las configuraciones extras.
   * @return string Configuraciones extras.
   */
  public function getExtra(){

    return $this->extra;
  
  }

  /**
   * Realizar casting a un valor por el tipo de datos del campo.
   * @return mixed Valor parseado.
   */
  public function parseValue($value){

    $fn = self::$parseFuncs[$this->getType()];
    return $fn($value);
    
  }

  /**
   * Convertir el campo a array.
   * @return array Campo convertir en array.
   */
  public function toArray(){

    // Atributos obligatorios
    $ret = array(
      'name' => $this->getName(),
      'type' => $this->getType(),
      'pk' => $this->isPk(),
      'allowNull' => $this->allowNull(),
    );

    // Atributos para campos numéricos.
    if(in_array($this->type, array('integer', 'float'))){
      $ret['unsigned'] = $this->isUnsigned();
      $ret['zerofill'] = $this->isZerofill();
      $ret['autoIncrement'] = $this->isAutoIncrement();
    }

    // Campos floats
    if($this->type == 'float'){
      $ret['precision'] = $this->getPrecision();
      $ret['scale'] = $this->getScale();
    }

    // Campos opcionales
    foreach(array(
      'defaultValue',
      'collage',
      'charset',
      'extra',
      'len',
    ) as $attr)
      if(isset($this->$attr) && trim($this->$attr)!=='')
        $ret[$attr] = $this->$attr;

    return $ret;

  }

}
