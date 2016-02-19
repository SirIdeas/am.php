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
     
    // -------------------------------------------------------------------------
    // $sch = AmScheme::get();
    // var_dump($sch->getTables());
    // var_dump($sch->generateScheme());
    // var_dump($sch->getGeneratedModels());
    // var_dump($sch->drop());
    // var_dump($sch->create());
    // exit;

    // -------------------------------------------------------------------------
    // // Crear tabla sin model
    // $tbl = AmScheme::table('usuarios')
    //   ->addField('id_user', array(
    //     'type' => 'integer',
    //     'pk' => true,
    //     'autoIncrement' => true
    //   ))
    //   ->addField('name', 'varchar')
    //   ->addField('sueldo', 'float')
    //   ->addCreatedAtField()
    //   ->addUpdatedAtField()
    //   ;
    // var_dump($tbl->drop());
    // var_dump($tbl->create());
    
    // -------------------------------------------------------------------------
    // // Crear tabla apartir del modelo
    // $tbl = Usuario::me();
    // var_dump($tbl->drop());
    // var_dump($tbl->create());
    // var_dump($tbl->truncate());

    var_dump(AmScheme::model('Usuario'));
    // $u = Usuario::me()->find(2);
    $u = new Usuario;

    $u->name .= 'a';
    $u->sueldo = 120.0;

    var_dump($u->save());
    var_dump($u->toArray());
    var_dump($u->getErrors());
    var_dump(Usuario::all()->get('array'));

    // $u = new User;
    // $u->s
    // 
  }

}