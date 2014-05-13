<?php
return array (
  'createRecord' => 
  array (
    'type' => 0,
    'description' => 'create a record',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'readRecord' => 
  array (
    'type' => 0,
    'description' => 'read a record',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'updateRecord' => 
  array (
    'type' => 0,
    'description' => 'update a record',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'deleteRecord' => 
  array (
    'type' => 0,
    'description' => 'delete a record',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'manageUsers' => 
  array (
    'type' => 0,
    'description' => 'create/update/delete users',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'createLocal' => 
  array (
    'type' => 1,
    'description' => 'create entity only in local area',
    'bizRule' => 'return in_array($params["post"]["location"], Yii::app()->user->location);',
    'data' => NULL,
  ),
  'updateLocal' => 
  array (
    'type' => 1,
    'description' => 'update entity only in local area',
    'bizRule' => 'return in_array($params["post"]["location"], Yii::app()->user->location);',
    'data' => NULL,
  ),
  'readLocal' => 
  array (
    'type' => 1,
    'description' => 'read entity only in local area',
    'bizRule' => 'return in_array($params["post"]["location"], Yii::app()->user->location);',
    'data' => NULL,
  ),
  'deleteLocal' => 
  array (
    'type' => 1,
    'description' => 'delete entity only in local area',
    'bizRule' => 'return in_array($params["post"]["location"], Yii::app()->user->location);',
    'data' => NULL,
  ),
  'createLocalUser' => 
  array (
    'type' => 1,
    'description' => 'create User entity only in local area',
    'bizRule' => 'return count(array_diff($_POST["location"], Yii::app()->user->location)) == 0;',
    'data' => NULL,
  ),
  'updateLocalUser' => 
  array (
    'type' => 1,
    'description' => 'update User entity only in local area',
    'bizRule' => 'return count(array_diff($_POST["location"], Yii::app()->user->location)) == 0;',
    'data' => NULL,
  ),
  'readLocalUser' => 
  array (
    'type' => 1,
    'description' => 'read User entity only in local area',
    'bizRule' => 'return count(array_diff($_POST["location"], Yii::app()->user->location)) == 0;',
    'data' => NULL,
  ),
  'deleteLocalUser' => 
  array (
    'type' => 1,
    'description' => 'delete User entity only in local area',
    'bizRule' => 'return count(array_diff($_POST["location"], Yii::app()->user->location)) == 0;',
    'data' => NULL,
  ),
  'Local Admin' => 
  array (
    'type' => 2,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
    'children' => 
    array (
      0 => 'createRecord',
      1 => 'createLocal',
      2 => 'createLocalUser',
      3 => 'updateRecord',
      4 => 'updateLocal',
      5 => 'updateLocalUser',
      6 => 'readRecord',
      7 => 'readLocal',
      8 => 'readLocalUser',
      9 => 'deleteRecord',
      10 => 'deleteLocal',
      11 => 'deleteLocalUser',
      12 => 'manageUsers',
    ),
    'assignments' => 
    array (
      57 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      84 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      86 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      87 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      97 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      102 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      103 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      104 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      105 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      106 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      63 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      108 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      116 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      115 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      120 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      123 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      124 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      126 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
    ),
  ),
  'School Representative' => 
  array (
    'type' => 2,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
    'children' => 
    array (
      0 => 'createRecord',
      1 => 'createLocal',
      2 => 'createLocalUser',
      3 => 'updateRecord',
      4 => 'updateLocal',
      5 => 'updateLocalUser',
      6 => 'readRecord',
      7 => 'readLocal',
      8 => 'readLocalUser',
      9 => 'deleteRecord',
      10 => 'deleteLocal',
      11 => 'deleteLocalUser',
    ),
    'assignments' => 
    array (
      96 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      99 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      100 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      101 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      109 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      110 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      113 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      114 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      117 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      119 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
    ),
  ),
  'Super Admin' => 
  array (
    'type' => 2,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
    'children' => 
    array (
      0 => 'createRecord',
      1 => 'createLocal',
      2 => 'createLocalUser',
      3 => 'updateRecord',
      4 => 'updateLocal',
      5 => 'updateLocalUser',
      6 => 'readRecord',
      7 => 'readLocal',
      8 => 'readLocalUser',
      9 => 'deleteRecord',
      10 => 'deleteLocal',
      11 => 'deleteLocalUser',
      12 => 'manageUsers',
    ),
    'assignments' => 
    array (
      85 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      107 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      3 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      11 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      61 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      62 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      64 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      69 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      70 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      71 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      72 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      82 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      83 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      88 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      89 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      90 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      91 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      92 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      98 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      111 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      112 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      118 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      121 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      122 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
      125 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
    ),
  ),
);
