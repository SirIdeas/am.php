<?php

class Usuario extends AmModel{

  protected
    $tableName = 'usuarios',
    $pks = array('id_user'),
    $fields = array(
      'id_user' => array(
        'type' => 'integer',
        'autoIncrement' => true,
      ),
      'name' => 'varchar',
      'email' => 'varchar',
      'sueldo' => 'float',
    ),
    $createdAtField = true,
    $updatedAtField = true;

}

class Main extends AmController{

  public function action_index(){
    
  }

  public function action_model(){
    
    // $this->addHeader('content-type:text/plain');
    $sch = AmScheme::get();
    // var_dump($sch->drop());
    // var_dump($sch->create());

    // $tbl = AmScheme::table('usuarios')
    //   ->addField('id_user', array(
    //     'type' => 'integer',
    //     'pk' => true,
    //     'autoIncrement' => true
    //   ))
    //   ->addField('name', array(
    //     'type' => 'varchar',
    //     'len' => 50,
    //   ))
    //   ->addField('sueldo', array(
    //     'type' => 'decimal',
    //     'len' => 2
    //   ))
    //   ->addCreatedAtField()
    //   ->addUpdatedAtField();
    // var_dump($tbl->drop());
    // var_dump($tbl->create());
    
    // $tbl = Usuario::me();
    // var_dump($tbl->drop());
    // var_dump($tbl->create());

    var_dump(AmScheme::model('Usuario'));

    // var_dump(Usuario::me()->truncate());

    $u = Usuario::me()->find(2);
    // $u = new Usuario;

    $u->name .= 'a';
    $u->sueldo = 120.0;

    var_dump($u->save());
    var_dump($u->toArray());
    var_dump($u->getErrors());

    var_dump(Usuario::all()->get('array'));

    // $u = new User;

    // $u->s

  }

}