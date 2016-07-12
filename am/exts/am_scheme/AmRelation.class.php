<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase general para instancias las relaciones.
 */
abstract class AmRelation extends AmObject{

  protected

    /**
     * Método permitidos
     */
    $allodMethods = true,

    /**
     * Instancia del registro al que pertenece el modelo relación
     */
    $record = null,

    /**
     * Instancia de la relación
     */
    $foreign = null;
    
  /**
   * Devuelve el nombre del model a la que referencia.
   * @return string Nombre del model.
   */
  public function getRecord(){
    
    return $this->record;

  }
    
  /**
   * Instancia de la la clave foránea.
   * @return string Nombre del model.
   */
  public function getForeign(){
    
    return $this->foreign;
    
  }

  /**
   * Devuelve la query para obtener los registros relacionados
   * @return AmQuery Instancia del query
   */
  protected function getQuery(){

    return $this->foreign->getQuery($this->record);

  }

  /**
   * Chequea que un modelo pertenezca al modelo de la realación.
   * @param  AmModel $model Modelo a chequear
   */
  protected function checkModel(AmModel $record){

    // Obtener el modelo de la relación.
    $model = $this->getForeign()->getModel();

    // Si el valor asignado es una instancia que no coresponde con el objeto
    // se genera un error.
    if($record !== null && !$record instanceof $model){
      throw Am::e('AMSCHEME_RELATION_SET_MUST_RECIVED_AMMODEL', $model);
    }

  }

  /**
   * Para detectar los métodos que no existen de una relación sin llamados desde
   * el objeto que retorne la relación.
   * @param  string $method    Nombre del método.
   * @param  array  $arguments Argumentos de la llamada.
   * @return mixed             Retorno de la llamada.
   */
  public function __call($method, $arguments){

    // Realizar llamado
    return call_user_func_array(array($this->_get(), $method), $arguments);

  }

  /**
   * Devuelve la instancia que deve devolverse en el llamaod __call del modelo.
   * @return $this
   */
  public function val(){
    
    return $this;

  }

  /**
   * Se debe implementar para que la clase devuelva un valor.
   * @return mixed Instancia de un objeto que depende del tipo de relación.
   */
  abstract public function _get();

  /**
   * Método que se ejecuta antes de guardar del registro propietario.
   */
  abstract public function beforeSave();

  /**
   * Método que se ejecuta después de guardar del registro propietario.
   */
  abstract public function afterSave();

  /**
   * Crea una instancia de una relación dependiendo del tipo
   * @param  [type] $record  [description]
   * @param  [type] $foreign [description]
   * @return [type]          [description]
   */
  public static function create(AmModel $record, AmForeign $foreign){

    // Obtener el nombre de la clase relación a instanciar.
    $className = 'Am'.camelCase($foreign->getType(), true).'Relation';

    // Crear instancia
    return new $className(array(
      'record' => $record,
      'foreign' => $foreign,
    ));

  }

}