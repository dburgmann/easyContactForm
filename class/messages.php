<?php

class Messages{
	private $messages 	= array();
	private $added		= array();
	
	public function __construct(){
		$this->messages['success'] 	= "Ihre Nachricht wurde erfolgreich versendet";
		$this->messages['tech']		= "Wir haben momentan technische Probleme, bitte probieren Sie es später noch einmal!";
		$this->messages['required'] = "Bitte füllen Sie alle Pflichtfelder aus!";
		$this->messages['email'] 	= "Das %s Feld muss eine gültige E-Mail Adresse enthalten!";
		$this->messages['text'] 	= "Das %s Feld darf keine xyz enthalten!";
		$this->messages['numeric'] 	= "Das %s Feld darf nur Zahlen enthalten!";
	}
	
	public function add($key, $value = ''){
		if(isSet($this->messages[$key])) $this->added[$key] = $value;
	}
	
	public function toString($prefix = ""){
		if(empty($this->added)) return '';
		
		$html  = '<div id="'.$prefix.'messageBox">';
		foreach ($this->added as $key => $value) {
			$html .= '<p>'.sprintf($this->messages[$key], ucfirst($value)).'</p>';
		}
		$html .= '</div>';
		
		return $html;
	}
}