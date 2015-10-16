<?php
class Admin_Form_Cms extends Zend_Form
{
	public function init()
	{
		// Set the method for the display form to POST
	}

	public function pageAdd()
	{
        $this->setMethod('post');
        $this->addAttribs(array('id'=>'frmPage'));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

		$position = new Zend_Form_Element_Text('position');
        $position->setLabel('pozitia');
        $position->setAttribs(array('class'=>'validate[required]'));
		$position->setRequired(true);
		$this->addElement($position);

		$title = new Zend_Form_Element_Text('title');
        $title->setLabel('titlu');
        $title->setAttribs(array('class'=>'validate[required]'));
		$title->setRequired(true);
		$this->addElement($title);

		$link = new Zend_Form_Element_Text('link');
        $link->setLabel('link-ul');
        $link->setAttribs(array('class'=>'validate[required]'));
		$link->setRequired(true);
		$this->addElement($link);

		$content = new Zend_Form_Element_Textarea('content');
        $content->setLabel('continut');
        $content->setAttribs(array());
		$content->setRequired(false);
		$this->addElement($content);

		$keywords = new Zend_Form_Element_Textarea('keywords');
        $keywords->setLabel('keywords');
        $keywords->setAttribs(array());
		$keywords->setRequired(false);
		$this->addElement($keywords);

		$description = new Zend_Form_Element_Textarea('description');
        $description->setLabel('descriere');
        $description->setAttribs(array());
		$description->setRequired(false);
		$this->addElement($description);

		$status = new Zend_Form_Element_Radio('status');
        $status->setLabel('status');
        $options = array('1'=>'activ', '0'=>'inactiv');
        $status->addMultiOptions($options);
        $status->addValidator(new Zend_Validate_InArray(array_keys($options)));
		$status->setValue('1');
        $status->setAttribs(array('class'=>'validate[required]'));
        $status->setRequired(true);
		$this->addElement($status);

		$submit = new Zend_Form_Element_Submit('submit');
        $submit->setValue('adauga');
        $submit->setAttribs(array('class'=>'button'));
        $submit->setIgnore(true);
		$this->addElement($submit);
	}

	public function pageEdit(Default_Model_Cms $model)
	{
		$this->position->setValue($model->getPosition());
		$this->title->setValue($model->getTitle());
		$this->keywords->setValue($model->getKeywords());
		$this->description->setValue($model->getDescription());
		$this->content->setValue($model->getContent());
		$this->link->setValue($model->getLink());
		$this->status->setValue($model->getStatus());
		$this->submit->setValue('modifica');
	}

	public function header()
	{
		$this->setMethod('post');
        $this->addAttribs(array('id'=>'frmCmsHeader'));

		$hf = new Zend_Form_Element_Hidden('hf');
		$hf->setValue('header');
		$this->addElement($hf);

		$position = new Zend_Form_Element_Text('position');
        $position->setLabel(Zend_Registry::get('translate')->_('cms_headfoot_table_position'));
		$position->setValue('header');
		$this->addElement($position);

		$content = new Zend_Form_Element_Textarea('content');
        $content->setLabel(Zend_Registry::get('translate')->_("cms_headfoot_table_content"));
		$content->setAttribs(array('style'=>'height:120px;'));
		$this->addElement($content);

		$status = new Zend_Form_Element_Radio('statush');
        $status->setLabel(Zend_Registry::get('translate')->_('cms_headfoot_table_status'));
        $optionStatus = array(Zend_Registry::get('translate')->_('cms_headfoot_table_inactive'), Zend_Registry::get('translate')->_('cms_headfoot_table_active'));
        $status->addMultiOptions($optionStatus);
        $status->addValidator(new Zend_Validate_InArray(array_keys($optionStatus)));
        $status->setAttribs(array('class'=>'validate[required]'));
		$status->setValue('0');
        $status->setSeparator('');
		$this->addElement($status);

		$submit = new Zend_Form_Element_Submit('submit');
        $submit->setValue(Zend_Registry::get('translate')->_('cms_headfoot_table_submit'));
        $submit->setAttribs(array('class'=>'button1'));
        $submit->setIgnore(true);
		$this->addElement($submit);
	}

	public function footer()
	{
		$this->setMethod('post');
        $this->addAttribs(array('id'=>'frmCmsFooter'));		

		$hf = new Zend_Form_Element_Hidden('hf');
		$hf->setValue('footer');
		$this->addElement($hf);

		$position = new Zend_Form_Element_Text('position');
        $position->setLabel(Zend_Registry::get('translate')->_('cms_headfoot_table_position'));
		$position->setValue('footer');
		$this->addElement($position);

		$content = new Zend_Form_Element_Textarea('content');
        $content->setLabel(Zend_Registry::get('translate')->_('cms_headfoot_table_content'));
        $content->setAttribs(array('style'=>'height:120px;'));   
		$this->addElement($content);     

		$status = new Zend_Form_Element_Radio('statusf');
        $status->setLabel(Zend_Registry::get('translate')->_('cms_headfoot_table_status'));
        $optionStatus = array(Zend_Registry::get('translate')->_('cms_headfoot_table_inactive'), Zend_Registry::get('translate')->_('cms_headfoot_table_active'));
        $status->addMultiOptions($optionStatus);
        $status->addValidator(new Zend_Validate_InArray(array_keys($optionStatus)));
        $status->setAttribs(array('class'=>'validate[required]'));
		$status->setValue('0');
        $status->setSeparator('');
		$this->addElement($status);

		$submit = new Zend_Form_Element_Submit('submit');
        $submit->setValue(Zend_Registry::get('translate')->_('cms_headfoot_table_submit'));
        $submit->setAttribs(array('class'=>'button1'));
        $submit->setIgnore(true);
		$this->addElement($submit);
	}

	public function seoAdd()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'frmSeo'));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

		$page = new Zend_Form_Element_Text('page');
		$page->setLabel(Zend_Registry::get('translate')->_('cms_add_seo_table_page'));
		$page->setAttribs(array('maxlength'=>'120', 'class'=>'validate[required,length[1,120]]'));
		$page->setRequired(true);
		$this->addElement($page);

		$title = new Zend_Form_Element_Text('title');
		$title->setLabel(Zend_Registry::get('translate')->_('cms_add_seo_table_title'));
		$title->setRequired(false);
		$this->addElement($title);

		$keyword = new Zend_Form_Element_Text('keyword');
		$keyword->setLabel(Zend_Registry::get('translate')->_('cms_add_seo_table_keywords'));
		$keyword->setRequired(false);
		$this->addElement($keyword);

		$description = new Zend_Form_Element_Text('description');
		$description->setLabel(Zend_Registry::get('translate')->_('cms_add_seo_table_description'));
		$description->setRequired(false);
		$this->addElement($description);

		$status = new Zend_Form_Element_Checkbox('status');
		$status->setLabel(Zend_Registry::get('translate')->_('cms_add_seo_table_is_active'));
		$status->setRequired(false);
		$status->setIgnore(true);
		$this->addElement($status);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setValue(Zend_Registry::get('translate')->_('cms_add_seo_table_submit'));
		$submit->setAttribs(array());
		$submit->setIgnore(true);
		$this->addElement($submit);
	}

	public function seoEdit(Default_Model_CmsSeo $value)
	{
		// Set the method for the display form to POST
		$this->title->setValue($value->getTitle());
		$this->keyword->setValue($value->getKeyword());
		$this->description->setValue($value->getDescription());
		$this->status->setValue($value->getStatus());
		$this->submit->setValue(Zend_Registry::get('translate')->_('cms_add_seo_table_modify'));
		$this->removeElement('page');
	}

	public function analyticsAdd()
	{
		// Set the method for the display form to POST
        $this->setMethod('post');
        $this->addAttribs(array('id'=>'frmAnalytics'));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

		$code = new Zend_Form_Element_Textarea('code');
        $code->setLabel(Zend_Registry::get('translate')->_('cms_add_google_analytics_table_tracking_code'));
        $code->setAttribs(array('class'=>'validate[required]'));
		$code->setRequired(true);
		$this->addElement($code);

		$status = new Zend_Form_Element_Radio('status');
        $status->setRequired(true);
        $status->setLabel(Zend_Registry::get('translate')->_("cms_add_google_analytics_table_status"));
        $optionStatus = array(Zend_Registry::get('translate')->_('cms_add_google_analytics_table_inactive'), Zend_Registry::get('translate')->_('cms_add_google_analytics_table_active'));
        $status->addMultiOptions($optionStatus);
        $status->addValidator(new Zend_Validate_InArray(array_keys($optionStatus)));
        $status->setAttribs(array('class'=>'validate[required]'));
        $status->setValue('1');
        $status->setSeparator('');
		$this->addElement($status);

		$submit = new Zend_Form_Element_Submit('submit');
        $submit->setValue(Zend_Registry::get('translate')->_('cms_add_google_analytics_table_submit'));
        $submit->setAttribs(array());
        $submit->setIgnore(true);
		$this->addElement($submit);

	}

	public function analyticsEdit(Default_Model_Analytics $value)
	{
		$this->code->setValue($value->getCode());
		$this->status->setValue($value->getStatus());
		$this->submit->setValue(Zend_Registry::get('translate')->_('cms_add_google_analytics_table_modify'));
	}
}
