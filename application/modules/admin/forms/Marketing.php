<?php
class Admin_Form_Marketing extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->addAttribs(array('id'=>'frmMarketing'));

        $code = new Zend_Form_Element_Textarea('code');
        $code->setLabel('Content');
        $code->setRequired(true);
        $this->addElement($code);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setValue('Modify');
        $this->addElement($submit);
    }

    public function edit(Default_Model_Marketing $model)
    {
        $this->code->setValue($model->getCode());
    }
}
