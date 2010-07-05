<?php
session_start();
unset($_SESSION['lazyForm']);
?>

<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Home - LazyStructure</title>
</head>
<body>
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
$formSettings = array('columns' => 2,
                      'onsubmit' => 'return formSubmit(this);',
                      'errors' => array (
                            'test1' => 'Required'));

$form = LazyForm::getInstance('test', $fields, $formSettings);
$form->generateForm();
$form->saveForm();
echo $form->getFormMarkup();
?>
</body>
</html>
