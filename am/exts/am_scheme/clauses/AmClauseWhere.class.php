<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
class AmClauseWhere extends AmClause{

  protected
    $wheres = array(),
    $union = false,
    $not = false,
    $in = false,
    $lastUnion = 'AND';

  public function __construct(array $data = array()){
    parent::__construct($data);

    $this->wheres = array();
    $this->union = false;
    $this->not = false;
    $this->in = false;
    $this->lastUnion = 'AND';

  }

  public function sql(){

    if(empty($this->wheres)){
      return '';
    }
    return '('.implode(' ', $this->wheres).')';

  }

  public function add(array $conditions){

    foreach ($conditions as $cond) {
      if(is_string($cond)){
        $cond = strtoupper($cond);
        if($cond === 'NOT'){
          if($this->not){
            throw Am::e('AMSCHEME_QUERY_REPEATED_NOT');
          }
          $this->not = true;
          continue;

        }elseif($cond === 'IN'){
          if($this->in){
            throw Am::e('AMSCHEME_QUERY_INVALID_IN');
          }
          $this->in = true;
          continue;

        }elseif($cond !== 'AND' && $cond !== 'OR'){
          throw Am::e('AMSCHEME_QUERY_UNKWON_OPERATOR', $cond);

        }elseif(!count($this->wheres)){
          continue;

        }elseif($this->union){
          throw Am::e('AMSCHEME_QUERY_BOOLEAN_OPERATOR_CONSECITIVE', $cond);

        }else{
          $this->lastUnion = $cond;
          $this->union = true;
        }
      }elseif(!is_array($cond)){
        throw Am::e('AMSCHEME_QUERY_INVALID_CONDITION', var_export($cond, true));

      }elseif($this->in){
        if(count($cond) != 2){
          throw Am::e('AMSCHEME_QUERY_INVALID_IN_ARGS_NUMBERS', var_export($cond, true));

        }elseif(!is_string($cond[0])){
          throw Am::e('AMSCHEME_QUERY_IN_FIRST_PARAM_MUST_BE_STRING', var_export($cond[0], true));

        }elseif(!(is_array($cond[1]) || $cond[1] instanceof AmQuery)){
          throw Am::e('AMSCHEME_QUERY_IN_SECOND_PARAM_MUST_BE_COLLECION', var_export($cond[1], true));

        }

        if(!$this->union && !empty($this->wheres)){
          $this->wheres[] = $this->lastUnion;
        }

        $cond = new AmClauseWhereItem(array(
          'query' => $this->query,
          'not' => $this->not,
          'cond' => array($cond[0], 'IN', $cond[1]),
        ));

        $this->not = false;
        $this->in = false;
        $this->union = false;

      }elseif(!self::hasAnyArray($cond)){

        if(!$this->union && !empty($this->wheres)){
          $this->wheres[] = $this->lastUnion;
        }

        if(count($cond)==2){
          $cond = array($cond[0], '=', $cond[1]);
        }

        $cond = new AmClauseWhereItem(array(
          'query' => $this->query,
          'not' => $this->not,
          'cond' => $cond,
        ));

        $this->not = false;
        $this->union = false;

      }else{

        if(!$this->union && !empty($this->wheres)){
          $this->wheres[] = $this->lastUnion;
        }

        $item = new self(array(
          'query' => $this->query,
          'not' => $this->not,
        ));

        $item->add($cond);

        $cond = $item;
        $this->not = false;
        $this->union = false;

      }

      $this->wheres[] = $cond;

    }

  }

  public static function hasAnyArray(array $conditions){

    foreach ($conditions as $key => $value) {
      if(is_array($value)){
        return true;
      }
    }
    
    return false;

  }

}