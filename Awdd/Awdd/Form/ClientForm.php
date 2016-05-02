<?php

namespace Awdd\Form;

use Awdd\Model\Client;

class ClientForm {
	
	
	public $elements;
	
	protected $filters;
	
	public $msg;
	
	protected $object;
	
	protected $isValid = null;
	
	public function __construct()
	{
		$this-
		$this->add(array(
			'name' => 'id',
			'type' => 'hidden',
		));
		
		$this->add(array(
			'name' => 'name',
			'type' => 'text',
			'options' => array(
	            'label' => 'Name',
	         ),
	        'attributes' => array(
	            'id' => 'clientName',
	         ),
		));
		
		$this->add(array(
			'name' => 'fone',
			'type' => 'text',
			'options' => array(
	            'label' => 'Fone',
	         ),
	        'attributes' => array(
	            'id' => 'clientFone',
	         ),
		));
		
		$this->add(array(
			'name' => 'email',
			'type' => 'text',
			'options' => array(
	            'label' => 'Email',
	         ),
	        'attributes' => array(
	            'id' => 'clientEmail',
	         ),
		));
		$this->getInputFilter();
	}
	
	private function add(Array $element)
	{
		$this->elements[$element['name']] = $element;
	}
	
	public function getInputFilter()
	{
		$this->addFilter(array(
			'name'       => 'id',
            'required'   => false,
            'filters' => array(
                array('name'    => 'Int'),
            ),		
		));
		
		$this->addFilter(array(
			'name'       => 'name',
            'required'   => true,
            'filters' => array(
                array('name' => 'StripTags' ), //'options' => array('allowable_tags' => '<p><b>')
	            array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array('name' => 'name'),
            ),			
		));
		
		$this->addFilter(array(
			'name'       => 'fone',
            'required'   => false,
            'filters' => array(
                array('name' => 'StripTags'),
	            array('name' => 'StringTrim'),
            ),		
		));
		
		$this->addFilter(array(
			'name'       => 'email',
            'required'   => true,
            'filters' => array(
                array('name' => 'StripTags'),
	            array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array('name' => 'email'),
            ),		
		));
		
		
	}
	
	private function addFilter(Array $element)
	{
		$this->filters[$element['name']] = $element;
	}
	
	public function getElement($elementName)
	{
		if (isset($this->elements[$elementName])):
			return $this->elements[$elementName];
		endif;
	}
	
	protected function getFilter($filterName)
	{
		if (isset($this->filters[$filterName])):
			return $this->filters[$filterName];
		endif;
	}
	
	public function formInput($element)
	{
		if (isset($element['name'])):
			$name = $element['name'];
		else:
			return null;
		endif;
		
		if (isset($element['type'])):
			$type = $element['type'];
		else:
			$type = 'text';
		endif;
		
		$attributes = "";
		if (isset($element['attributes'])):
			foreach ($element['attributes'] as $key => $attribute) :
				$attributes .= sprintf(" %s = \"%s\"", $key, $attribute);
			endforeach;
		endif;
		
		$input = sprintf("<input name=\"%s\" type=\"%s\"  %s> ", $name, $type, $attributes);
		
		return $input;
		
	}
	
	public function formLabel($element)
	{
		if (isset($element['options']['label'])):
			$label = sprintf("<label for=\"%s\">%s</label>", $element['name'], $element['options']['label']);
		else:
			$label =  sprintf("<label for=\"%s\">%s</label>", $element['name'], $element['name']);
		endif;
		
		$filter = $this->getFilter($element['name']);
		
		if ($filter && isset($filter['required']) && $filter['required'] === TRUE):
			$label .= ' <span class="description">(required)</span >';
		endif;
		
		return $label;
	}
	
	public function errorMsg($element)
	{
		$html = '';
		if (isset($this->msg[$element['name']])):
			$html = '<ul>';
			foreach ($this->msg[$element['name']] as $error) 
			{
				$html .= sprintf("<li>%s : %s</li>", $element['name'], $error);
			}
			$html .= '</ul>';
		endif;
		return $html;
	}
	
	public function bind($values)
	{
		foreach ($values as $key => $value) :
			if(isset($this->elements[$key])):
				$this->elements[$key]['attributes']['value'] = $value;
			endif;
		endforeach;
	}
	
	public function isValid()
	{
		foreach ($this->elements as $element) :
			$filter = $this->filters[$element['name']];
			if ($filter['filters'] ):
				foreach ($filter['filters'] as $key => $fil) :
					switch ($fil['name']) :
						case 'int':
							$this->elements[$element['name']]['attributes']['value'] = (int) $element['attributes']['value'];
	 						break;
							
						case 'StripTags':
							$this->elements[$element['name']]['attributes']['value'] = strip_tags($this->elements[$element['name']]['attributes']['value'], $fil['options']['allowable_tags']);
							break;
							
						case 'StringTrim':
							$this->elements[$element['name']]['attributes']['value'] = trim($this->elements[$element['name']]['attributes']['value']);
							break;
					endswitch;
				endforeach;
			endif;
			if ($filter['validators']):
				foreach ($filter['validators'] as $key => $validator) :
					switch ($validator['name']) :
						case 'email':
							$email = $this->elements[$element['name']]['attributes']['value'];
							if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
								$this->msg[$element['name']][] = "Invalid email format"; 
								$this->isValid = False;
							}
	 						break;
							
						case 'name':
							$name = $this->elements[$element['name']]['attributes']['value'];
							if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
								$this->msg[$element['name']][] = "Only letters and white space allowed"; 
								$this->isValid = False;
							}
	 						break;
					endswitch;
				endforeach;
			endif;
		endforeach;
		if ($this->isValid !== FALSE):
			$this->isValid = TRUE;
			$this->hydrateObject();
		endif;
		
		
		return $this->isValid;
	}
	
	public function setObject($value)
	{
		$this->object = $value;
	}
	
	public function getObject()
	{
		return $this->object;
	}
	
	protected function hydrateObject()
	{
		$object = $this->object;
		foreach ($this->elements as $element) :
			if (method_exists($object, 'set' . ucfirst($element['name']))) {
                $object->{'set' . ucfirst($element['name'])}($element['attributes']['value']);
            }
		endforeach;
			
	}
	
}
