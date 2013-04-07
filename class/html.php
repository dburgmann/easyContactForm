<?php
class html {

	public static function input($name, $value = '', $prefix = '', $suffix = '') {
		return '<input type="text" name="'.$prefix.$name.'" id="'.$prefix.$name.'" value="'.$value.'" '.$suffix.' />';
	}
	
	public static function textarea($name, $value = '', $prefix = '', $suffix = ''){
		return '<textarea name="'.$prefix.$name.'" id="'.$prefix.$name.'" cols="10" rows="10" '.$suffix.'>'.$value.'</textarea>';
	}
	
	public static function dropdown($name, $options, $value = '', $prefix = '', $suffix = ''){
		$html = '<select name="'.$prefix.$name.'" id="'.$prefix.$name.'"">';
		foreach ($options as $option => $value) {
			$html .= '<option value="'.$value.'">'.$option.'</option>';	
		}
	
		$html .= '</select>';
		return $html;
	}
	
	public static function label($name, $prefix = '', $mandatory = false){
		$mandatoryStr = ($mandatory) ? '<span class="'.$prefix.'mandatory">*</span>' : '';
		return  '<label for="'.$prefix.$name.'">'.ucfirst($name).$mandatoryStr.':</label>';
	}

}
