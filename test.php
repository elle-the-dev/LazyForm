<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('LazyForm.class.php');

$fields = array('test1' => array (
                    'label' => 'test1',
                    'type' => 'select',
                    'size' => 3,
                    'items' => array ('one', 'two', 'three')),
                'test2' => array (
                    'fieldColspan' => 2,
                    'type' => 'button',
                    'value' => 'Test 2')
          );
$formSettings = array('columns' => 2);

$form = new LazyForm($fields, $formSettings);
$form->generateForm();
echo $form->getFormMarkup();
?>
