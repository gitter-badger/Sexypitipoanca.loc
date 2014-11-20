<?php
class Admin_Form_Login extends Zend_Form 
{
	function init()
	{
        // Set the method for the display form to POST
        $this->setMethod('post');
        
        // Add an email element
        $this->addElement(
			'text', 'tbUser', array(
            'label'      => 'Nume utilizator',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'attribs'    => array('class'=>'f1'),
            'validators' => array(
                'alnum',
                array('stringLength', false, array(3, 20)),
            )
        ));

        // Add an password element
        $this->addElement(
			'password', 'tbPass', array(
            'label'      => 'Parola',
            'attribs'    => array('class'=>'f1'),
            'required'   => true,
        ));

        $this->addElement(
			'submit', 'submit', array(
            'ignore'   => true,
            'attribs'    => array('class'=>'bt1'),
            'label'    => 'Autentificare',
			'value'		=> 'Autentificare'
        ));

	}
}
