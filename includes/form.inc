<?php
class form{
	
	public $fields;
	public $errors;
	public $error_messages;
	public $stage;
	
	
	
	function form_start($stage){
		$this->fields = array();
		$this->stage = $stage;
		print '<form id="stage'.$stage.'" name="stage'.$stage.'" method="POST" action="'.($stage+1).'.php" enctype="multipart/form-data">';
	}

	function text($name, $length, $cur_val='', $array=0){
		if(strpos($name, '['))
			$this->fields[] = substr($name, 0, strpos($name, '['));
		else 
			$this->fields[] = $name;
		print '<input type="text" name="'.$name.($array?'[]':'').'" id="'.$name.'" class="'.$this->errors[$name].'" value="'.htmlentities($cur_val, ENT_QUOTES).'" maxlength="'.$length.'" />';
	}
	
	function dropdown($name, $values, $cur_val='', $array=0, $extra=''){
		if(strpos($name, '['))
			$this->fields[] = substr($name, 0, strpos($name, '['));
		else 
			$this->fields[] = $name;
		print '<select name="'.$name.($array?'[]':'').'" class="'.$this->errors[$name].'" '.$extra.'>';
			foreach($values as $key => $value)
				print '<option value="'.$key.'" '.(($cur_val==$key)?'selected':'').'>'.$value.'</option>';
		print '</select>';
	}
	
	function radio($name, $value, $cur_val=''){
	if(strpos($name, '['))
			$this->fields[] = substr($name, 0, strpos($name, '['));
		else 
			$this->fields[] = $name;
		print '<input type="radio" name="'.$name.'" class="'.$this->errors[$name].'" value="'.$value.'" '.($value==$cur_val?'checked':'').'/>';
	}
	
	function textArea($name, $cur_val=''){
		if(strpos($name, '['))
			$this->fields[] = substr($name, 0, strpos($name, '['));
		else 
			$this->fields[] = $name;
		print '<textarea name="'.$name.'" class="'.$this->errors[$name].'">'.htmlentities($cur_val, ENT_QUOTES).'</textarea>';
	}
	
	function checkbox($name, $value, $cur_vals = array(), $array = 1, $extra=''){
		if(strpos($name, '['))
			$this->fields[] = substr($name, 0, strpos($name, '['));
		else 
			$this->fields[] = $name;
		print '<input type="checkbox" name="'.$name.($array?'[]':'').'" class="'.$this->errors[$name].'" value="'.$value.'" '.(in_array($value, $cur_vals)?'checked':'').' '.$extra.'/>';
	}
	function hidden($name, $value, $array=0){
		if(strpos($name, '['))
			$this->fields[] = substr($name, 0, strpos($name, '['));
		else 
			$this->fields[] = $name;
		print '<input type="hidden" name="'.$name.($array?'[]':'').'"  value="'.htmlentities($value, ENT_QUOTES).'" />';
	}
	function file_upload($name){
		if(strpos($name, '['))
			$this->fields[] = substr($name, 0, strpos($name, '['));
		else 
			$this->fields[] = $name;
		print '<input type="file" name="'.$name.'" />';
	}
	function submit(){
		print '<input type="submit" value="submit" />';
	}
	
	
	function form_finish($stage){
		$this->fields = array_unique($this->fields);
		print '<input type="hidden" name="stage" value="'.$stage.'" /><input type="hidden" name="fields" value="'.implode(',',$this->fields).'" /></form>';
	}

	
/*
	function validate(){
		$error = 0;
		$this->error_messages = '';
		$this->errors = array();
		$this->fields = explode(',',$_POST['fields']);
		foreach($this->fields as $field){
			switch($field){
				case 'firstName':
					if(empty($_POST[$field])){
						$error = 1;
						$this->errors[$field] = 'error';
						$this->error_messages .= 'Missing first name.<br />';
					}
					else{
						$this->application->application_values[$field] = $_POST[$field];
					}
				break; 
			}
		}
		if($error){
			header("Location:".$_POST['stage'].".php");
			exit();
		}
	}
*/
}
