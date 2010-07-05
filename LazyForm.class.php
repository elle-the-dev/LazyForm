<?php
class LazyForm
{
    private $forms;
    private $name;
    private $fields;
    private $formSettings;
    private $formMarkup;

    public function LazyForm($name=null, $fields=null, $formSettings=null)
    {
        if(isset($name))
        {
            $form = new LazyForm();
            $form->name = $name;
            $form->fields = $fields;
            $form->formSettings = $formSettings;
            $this->forms[$name] = $form;
        }
    }

    public function getInstance($name, $fields=false, $formSettings=false)
    {
        if(isset($_SESSION['lazyForm']))
        {
            $form = unserialize($_SESSION['lazyForm']);
            $form = $form->getForm($name);
            if(!$form)
                return self::createForm($name, $fields, $formSettings);
            else
                return $form;
        }
        else
        {
            $form = new LazyForm($name, $fields, $formSettings);
            $_SESSION['lazyForm'] = serialize($form);
            return $form->getForm($name);
        }
    }

    public function saveForm()
    {
        $form = unserialize($_SESSION['lazyForm']);
        $form->setForm($this);
        $_SESSION['lazyForm'] = serialize($form);
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    private static function createForm($name, $fields, $formSettings)
    {
        $form = new LazyForm($name, $fields, $formSettings);
        $form->setName($name);
        return $form->getForm($name);
    }

    public function setForm($form)
    {
        $this->forms[$form->getName()] = $form;
    }

    public function getForm($name)
    {
        if(isset($this->forms[$name]))
            return $this->forms[$name];
        else
            return false;
    }

    public function render()
    {
        echo $this->formMarkup;
    }

    public function generateForm()
    {
        /*
            generateTable does just what it says: generates the table
            The available settings are specified below along with valid options
        */
        /*
            formSettings['id']          = "lazyForm" [string]
            Denotes the ID attribute of the HTML form element created
           
            formSettings['class']       = "lazyForm" [string]
            Denotes the CLASS attribute of the HTML form element created

            formSettings['method']      = "post" [string]
            Denotes the METHOD attribute of the HTML form element created

            formSettings['action']      = $_SERVER['PHP_SELF'] [string]
            Denotes the ACTION attribute of the HTML form element created

            formSettings['onsubmit']    = null [string]
            Acceps JavaScript code to go into the onsubmit even call of the form

            formSettings['heading']     = null [string]
            The heading placed at the top of the form

            formSettings['values']      = null [array]
            Values to be filled in the form elements. The key is matched to the field name.

            formSettings['errors']      = null [array]
            Errors to be outputted next to form elements. The key is matched to the field name.

            formSettings['columns']     = 1 [int]
            The number of columns to display the data in. A column is defined as a single value from $fields[]

            formSettings['order']       = "left-to-right" [string]
            Denotes the order of the display of form elements. Options are "leftToRight" and "topToBottom"



            fields[]['id']          = [name] [string]
            Denotes the ID attribute of the field

            fields[]['class']       = null [string]
            Denotes the CLASS attribute of the field

            fields[]['type']        = "text" [string]
            Denotes the TYPE attribute of the field

            fields[]['tabindex']    = null [int]
            Denotes the TABINDEX attribute of the field

            fields[]['value']       = "" [string]
            Denotes the VALUE attribute of the field

            fields[]['size']        = "" [int]
            Denotes the SIZE attribute of the field

            fields[]['label']       = name [string]
            The label to appear beside the element

            fields[]['items']      = null
            The items to appear in a select or radio list. The key is the value of the element, the value is the label

            fields[]['labelColspan']    = 1
            Denotes the COLSPAN attribute of the containing table cell of the label

            fields[]['labelRowspan']    = 1
            Denotes the ROWSPAN attribute of the containing table cell of the label

            fields[]['fieldColspan']    = 1
            Denotes the COLSPAN attribute of the containing table cell of the field

            fields[]['fieldRowspan']    = 1
            Denotes the ROWSPAN attribute of the containing table cell of the field

            fields[]['validation']  = null [string]
            The type of validation to apply to the field. Options are "phone", "email", "postalCode", "zipCode", "alphaNumeric", or a regex string

            fields[]['required']    = true [bool]
            Whether the field is required on validation

        */

            $fields = $this->fields;
            $formSettings = $this->formSettings;

            $fieldDefaults = array (
                    'class'         => null,
                    'type'          => 'text',
                    'tabindex'      => null,
                    'value'         => "",
                    'label'         => false,
                    'size'          => 1,
                    'items'         => null,
                    'labelColspan'  => 1,
                    'labelRowspan'  => 1,
                    'fieldColspan'  => 1,
                    'fieldRowspan'  => 1,
                    'validation'    => null
            );

            $formDefaults = array (
                    'id'            => 'lazyForm',
                    'class'         => 'lazyForm',
                    'method'        => 'post',
                    'action'        => $_SERVER['PHP_SELF'],
                    'onsubmit'      => null,
                    'heading'       => null,
                    'values'        => null,
                    'errors'        => null,
                    'columns'       => 1,
                    'order'         => 'leftToRight'
            );

            // Setting the default form settings
            isset($formSettings['id'])          ? $id               = $formSettings['id']             : $id             = $formDefaults['id'];
            isset($formSettings['class'])       ? $class            = $formSettings['class']          : $class          = $formDefaults['class'];
            isset($formSettings['method'])      ? $method           = $formSettings['method']         : $method         = $formDefaults['method'];
            isset($formSettings['action'])      ? $action           = $formSettings['action']         : $action         = $formDefaults['action'];
            isset($formSettings['onsubmit'])    ? $onsubmit         = $formSettings['onsubmit']       : $onsubmit       = $formDefaults['onsubmit'];
            isset($formSettings['heading'])     ? $heading          = $formSettings['heading']        : $heading        = $formDefaults['heading'];
            isset($formSettings['values'])      ? $values           = $formSettings['values']         : $values         = $formDefaults['values'];
            isset($formSettings['errors'])      ? $errors           = $formSettings['errors']         : $errors         = $formDefaults['errors'];
            isset($formSettings['columns'])     ? $columns          = $formSettings['columns']*2      : $columns        = $formDefaults['columns']*2;
            isset($formSettings['order'])       ? $order            = $formSettings['order']          : $order          = $formDefaults['order'];

            isset($onsubmit) ? $onsubmitStr = ' onsubmit="'.$onsubmit.'"' : $onsubmitStr = "";

            $output = <<<TEMPLATE

<form id="{$id}" class="{$class}" method="{$method}" action="{$action}"{$onsubmitStr}>
<table border="2">
    <tr>

TEMPLATE;

            $column = 0;
            foreach($fields AS $key => $field)
            {
                $fieldDefaults['id'] = $key;
                isset($field['id'])         ? $fId          = $field['id']          : $fId          = $fieldDefaults['id'];
                isset($field['class'])      ? $fClass       = $field['class']       : $fClass       = $fieldDefaults['class'];
                isset($field['type'])       ? $fType        = $field['type']        : $fType        = $fieldDefaults['type'];
                isset($field['tabindex'])   ? $fTabindex    = $field['tabindex']    : $fTabindex    = $fieldDefaults['tabindex'];
                isset($field['value'])      ? $fValue       = $field['value']       : $fValue       = $fieldDefaults['value'];
                isset($field['size'])       ? $fSize        = $field['size']        : $fSize        = $fieldDefaults['size'];
                isset($field['label'])      ? $fLabel       = $field['label']       : $fLabel       = $fieldDefaults['label'];
                isset($field['items'])      ? $fItems       = $field['items']       : $fItems       = $fieldDefaults['items'];
                isset($field['labelColspan'])   ? $fLabelColspan    = $field['labelColspan']    : $fLabelColspan    = $fieldDefaults['labelColspan'];
                isset($field['labelRowspan'])   ? $fLabelRowspan    = $field['labelRowspan']    : $fLabelRowspan    = $fieldDefaults['labelRowspan'];
                isset($field['fieldColspan'])   ? $fFieldColspan    = $field['fieldColspan']    : $fFieldColspan    = $fieldDefaults['fieldColspan'];
                isset($field['fieldRowspan'])   ? $fFieldRowspan    = $field['fieldRowspan']    : $fFieldRowspan    = $fieldDefaults['fieldRowspan'];
                isset($field['validation'])     ? $fValidation      = $field['validation']      : $fValidation      = $fieldDefaults['validation'];


                $fSize === 1 ? $size = "" : $size = ' size="'.$fSize.'"';
                $type = strtolower($fType);

                if($type == "select")
                {
                    $fieldMarkup = <<<TEMPLATE
<select id="{$fId}" name="{$key}"{$size}>
TEMPLATE;
                    foreach($fItems AS $value => $label)
                    {
                        $fieldMarkup .= <<<TEMPLATE

                <option value="{$value}">{$label}</option>
TEMPLATE;
                    }
                    $fieldMarkup .= <<<TEMPLATE
                    
            </select>
TEMPLATE;
                }
                else if($type == "radio")
                {
                    $fieldMarkup = "";
                    foreach($fItems AS $value => $label)
                    {
                        $fieldMarkup .= <<<TEMPLATE
<label for="{$value}">{$label}<input type="radio" name="{$key}" id="{$value}" value="{$value}" /></label>
TEMPLATE;
                    }
                }
                else
                {
                    $fieldMarkup = <<<TEMPLATE
<input type="{$fType}" name="{$key}" id="{$fId}" value="{$fValue}" />
TEMPLATE;
                }


                if($column >= $columns)
                {
                    $output .= <<<TEMPLATE

    <tr>    
TEMPLATE;
                    $column = 0;
                }

                if($fLabel)
                {
                    $fLabelColspan === 1 ? $labelColspan = "" : $labelColspan = ' colspan="'.$fLabelColspan.'"';    
                    $output .= <<<TEMPLATE
        <td{$labelColspan}>{$fLabel}</td>
TEMPLATE;
                    $column += $fLabelColspan;
                }

                if($column >= $columns)
                {
                    $output .= '</tr><tr>';
                    $column = 0;
                }


                $fFieldColspan === 1 ? $fieldColspan = "" : $fieldColspan = ' colspan="'.$fFieldColspan.'"';    
                isset($errors[$key]) ? $error = $errors[$key] : $error = "";
                $output .= <<<TEMPLATE

        <td{$fieldColspan}>
            {$fieldMarkup}{$error}
        </td>
TEMPLATE;
                $column += $fFieldColspan;
                if($column >= $columns)
                    $output .= '</tr>';
            }
            $output .= '</table></form>';

            $this->formMarkup = $output;
    }

    public function validate()
    {
        /*
            Not usable in current state
        */
        $this->errors = array();
        $this->fields;
        foreach($this->fields AS $key => $field)
        {
            $this->errors[] = self::validateField($field['value'], $field['validation']);
        }
        return isset($errors[0]);
    }

    public function validateField($value, $validation)
    {
        switch($validation)
        {
            case "required" : return $this->validateRequired($value); break;
            case "phone" : return $this->validatePhone($value); break;
            case "email" : return $this->validateEmail($value); break;
            case "alnum" : return $this->validateAlnum($value); break;
            case "username" : return $this->validateUsername($value); break;
            default: return $this->validateRegex($value, $validation);
        }
    }

    private function validateRequired($value)
    {
        return !empty($value);
    }

    private function validatePhone($value)
    {
        return preg_match('/[0-9]{10}/', preg_replace('/[^0-9]*/', $value));
    }

    private function validateEmail($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    private function validateAlnum($value)
    {
        return ctype_alnum($value);
    }

    private function validateUsername($value)
    {
        $valid = array('_', '-', '.');
        return ctype_alnum(str_replace($valid, '', $value));
    }

    private function validateRegex($value, $regex)
    {
        return preg_match($regex, $value);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getFormMarkup()
    {
        return $this->formMarkup;
    }
}
?>
