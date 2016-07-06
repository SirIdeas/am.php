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
    $wheres = null;

  public function __construct(array $data = array()){
    parent::__construct($data);

    $this->wheres = array();

  }

  public function getWheres(){

    return $this->wheres;

  }

  public function getNot(){

    return $this->not;

  }

  public function sql(){

    if(empty($this->wheres)){
      return '';
    }
    return '('.implode(' ', $this->wheres).')';

  }

  public function add(array $conditions){

    $not = false;
    $in = false;
    $union = false;
    $lastUnion = 'AND';

    foreach ($conditions as $cond) {
      if(is_string($cond)){
        $cond = strtoupper($cond);
        if($cond === 'NOT'){
          if($not){
            throw Am::e('AMSCHEME_QUERY_REPEATED_NOT');
          }
          $not = true;
          continue;

        }elseif($cond === 'IN'){
          if($in){
            throw Am::e('AMSCHEME_QUERY_INVALID_IN');
          }
          $in = true;
          continue;

        }elseif($cond !== 'AND' && $cond !== 'OR'){
          throw Am::e('AMSCHEME_QUERY_UNKWON_OPERATOR', $cond);

        }elseif(!count($this->wheres)){
          continue;

        }elseif($union){
          throw Am::e('AMSCHEME_QUERY_BOOLEAN_OPERATOR_CONSECITIVE', $cond);

        }else{
          $lastUnion = $cond;
          $union = true;
        }
      }elseif(!is_array($cond)){
        throw Am::e('AMSCHEME_QUERY_INVALID_CONDITION', var_export($cond, true));

      }elseif($in){
        if(count($cond) != 2){
          throw Am::e('AMSCHEME_QUERY_INVALID_IN_ARGS_NUMBERS', var_export($cond, true));

        }elseif(!is_string($cond[0])){
          throw Am::e('AMSCHEME_QUERY_IN_FIRST_PARAM_MUST_BE_STRING', var_export($cond[0], true));

        }elseif(!(is_array($cond[1]) || $cond[1] instanceof AmQuery)){
          throw Am::e('AMSCHEME_QUERY_IN_SECOND_PARAM_MUST_BE_COLLECION', var_export($cond[1], true));

        }

        if(!$union && !empty($this->wheres)){
          $this->wheres[] = $lastUnion;
        }

        $cond = new AmClauseWhereItem(array(
          'query' => $this->query,
          'not' => $not,
          'cond' => array($cond[0], 'IN', $cond[1]),
        ));

        $not = false;
        $in = false;
        $union = false;

      }elseif(!self::hasAnyArray($cond)){

        if(!$union && !empty($this->wheres)){
          $this->wheres[] = $lastUnion;
        }

        if(count($cond)==2){
          $cond = array($cond[0], '=', $cond[1]);
        }

        $cond = new AmClauseWhereItem(array(
          'query' => $this->query,
          'not' => $not,
          'cond' => $cond,
        ));

        $not = false;
        $union = false;

      }else{

        if(!$union && !empty($this->wheres)){
          $this->wheres[] = $lastUnion;
        }

        $item = new self(array(
          'query' => $this->query,
          'not' => $not,
        ));

        $item->add($cond);

        $cond = $item;
        $not = false;
        $union = false;

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