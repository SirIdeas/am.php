<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para las relaciones entre las tablas.
 */
class AmRelation extends AmObject{

  protected
    
    /**
     * Nombre del esquema al que apunta la relación.
     */
    $scheme = '',
    
    /**
     * Nombre de la tabla a la que apunta la relación.
     */
    $table = null,
    
    /**
     * Instancia de la tabla a la que apunta la relación.
     */
    $tableInstance = null,
    
    /**
     * Hash de columnas relacionadas.
     */
    $columns = array();
    
  /**
   * Contructor. Inicializa la tabla.
   */
  public function __construct($data = null){
    parent::__construct($data);

    // Obtener la instancia de la tabla
    $this->tableInstance = AmScheme::table($this->getTable(),
      $this->getScheme());

  }
    
  /**
   * Devuelve el nombre del esquema a la que referencia.
   * @return  string  Nombre del esquema.
   */
  public function getScheme(){
    
    return $this->scheme;

  }

  /**
   * Devuelve el nombre de la tabla a la que referencia.
   * @return  string  Nombre de la tabla.
   */
  public function getTable(){
    
    return $this->table;

  }

  /**
   * Devuelve el hash con las columnas relacionadas.
   * @return  hash  Hash con las columnas relacionadas.
   */
  public function getColumns(){
    
    return $this->columns;

  }

  /**
   * Generador de la consulta para la relación basado en un modelo.
   * @param   AmModel   $model  Instancia de AmModel a la que se quiere obtener
   *                            la relación.
   * @return  AmQuery           Consulta generada.
   */
  public function getQuery(AmModel $model){

    // Una consulta para todos los registros de la tabla
    $q = $this->tableInstance->all();

    foreach($this->getColumns() as $from => $to){
      $q->where("{$to}='{$model->$from}'");
    }

    return $q;

  }

  /**
   * Devuelve una array con los datos de la relación.
   * @return  array   Relación como un array.
   */
  public function toArray(){

    return array(
      'scheme' => $this->getScheme(),
      'table' => $this->getTable(),
      'columns' => $this->getColumns()
    );

  }

}
