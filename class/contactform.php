<?php
require_once('valid.php');
require_once('html.php'); 
require_once('messages.php');          
require_once('phpmailer/class.phpmailer.php');

class ContactForm {
    private $messages	= null;
   
   	private $prefix		= "";
    private $fields     = array();
    private $values 	= array();
    private $mandatory	= array();

	private $isSmtp          = "";
	private $smtpServer      = "";
	private $smtpPort        = "";
	private $smtpUser        = "";
	private $smtpPass        = "";
	private $method			 = "";

    private $owner           = "";
    private $domain          = "";
    private $processingPage  = "";
    private $sendingEmail    = "";
    private $receivingEmail  = "";



	/**
	*Constructor
	**/
    public function  __construct() {
    	$this->messages	= new Messages();
        $this->loadConfig();
    }


    /**
    *loads the config file
    **/
    private function loadConfig(){
        require_once("config/config.php");
        $this->isSmtp          	= $config['isSmtp'];
        $this->smtpServer       = $config['smtpServer'];
        $this->smtpPort         = $config['smtpPort'];
        $this->smtpUser         = $config['smtpUser'];
        $this->smtpPass         = $config['smtpPass'];
        $this->method			= $config['method'];
        
        $this->sendingEmail     = $config['sendingEmail'];
        $this->receivingEmail   = $config['receivingEmail'];
        $this->owner            = $config['name'];
        $this->domain           = $config['domain'];
        $this->processingPage   = $config['processingPage'];
        
        $this->prefix			= $config['prefix'];
        $this->fields   		= $config['fields'];
        $this->mandatory		= $config['mandatory'];
        $this->validation		= $config['validation'];
    }


    /**
    *Generartes the contact form html
    **/
    public function form(){
        $form = '<form accept-charset="utf-8" action="'.$this->processingPage.'" method="'.$this->method.'" name="'.$this->prefix.'form" id="'.$this->prefix.'form">';
        $htmlFields = $this->fieldsToHtml($this->fields);
        foreach($htmlFields as $field => $html){
        	$form .= html::label($field, $this->prefix, in_array($field, $this->mandatory));
        	$form .= $html;
        }
        $form .= '<input type="submit" name="'.$this->prefix.'submit" id="'.$this->prefix.'submit" value="Senden" />';
        $form .= '</form>';
        $return ='<div id="'.$this->prefix.'wrapper">'.$this->messages->toString($this->prefix).trim($form).'</div>';         
        return $return;
    }
	

    /**
    * Generates the html for the optional additional fields specified in config.ini
    **/
    private function fieldsToHtml(){
        $html = array();

        foreach ($this->fields as $name => $type) {
        	$value = (isSet($this->values[$name]))? $this->values[$name] : '';
			if(is_array($type)){
				$html[$name] = html::dropdown($name, $type, $value, $this->prefix);
			}
			elseif($type === 'input'){
				$html[$name] = html::input($name, $value, $this->prefix);
			}
			elseif($type === 'textarea'){
				$html[$name] = html::textarea($name, $value, $this->prefix);
			}
			else{
				$this->messages->add('tech');
				return FALSE;
			}
        }
        return $html;
    }

		
	/**
	*Processes the submitted form
	*/
    public function process(){
    	if(!Valid::getValue($this->prefix.'submit', false)) return;
    	$this->formValues();
    	
    	if(!$this->validate()) return;
        
        if($this->sendMail()) $this->messages->add('success');
        else $this->messages->add('tech');
        $this->resetForm();
    }


    /**
    *Gets all Values from the form and sets the class attributes according to it
    **/
    private function formValues(){
        foreach ($this->fields as $name => $type) {
        	$this->values[$name] = Valid::getFormValue($this->prefix.$name);
        }
		return true;
    }

    private function resetForm(){
        foreach ($this->values as $name => $value) {
            $this->values[$name] ="";
        }
        return true;
    }


	/**
	*Validates submitted post data from the form
	**/
	private function validate(){
		$validated = TRUE;
		foreach ($this->validation as $name => $rules) {
			foreach ($rules as $rule) {
				$valid 		= TRUE;
				$hasValue 	= (isSet($this->values[$name]) && !empty($this->values[$name]));
				switch($rule){
					case 'required':
						$valid = $hasValue;
						if(!$valid){ 
							$this->messages->add('required', $name);
							$validated = FALSE;
						}
						break;
					case 'text':
						if($hasValue) $valid = Valid::isStandardText($this->values[$name]);
						if(!$valid){ 
							$this->messages->add('text', $name);
							$validated = FALSE;
						}
						break;
					case 'email':
						if($hasValue) $valid = Valid::isEmail($this->values[$name]);
						if(!$valid){ 
							$this->messages->add('email', $name);
							$validated = FALSE;
						}
						break;
					case 'numeric':
						if($hasValue) $valid = Valid::isNumber($this->values[$name]);
						if(!$valid){ 
							$this->messages->add('numeric', $name);
							$validated = FALSE;
						}
						break;
				}
			}
		}
		return $validated;      
	}


    /**
    *Sends the mail
    **/
    private function sendMail(){
        $mail = new PHPMailer(true); //false => No Exceptions are thrown, use true for debug only
        
        try {
            if($this->isSmtp){
                $mail->IsSMTP();
                $mail->Port       = 26;                     // set the SMTP port for the GMAIL server
                $mail->SMTPAuth   = true;                   // enable SMTP authentication
                $mail->Host       = $this->smtpServer;      // SMTP server
                $mail->Port       = $this->smtpPort;
                $mail->Username   = $this->smtpUser;        // SMTP account username
                $mail->Password   = $this->smtpPass;        // SMTP account password
            }
            $mail->SetFrom($this->sendingEmail, $this->domain);
            $mail->AddAddress($this->receivingEmail, $this->owner);

            $mail->Subject  = "Kontaktformular Nachricht von ".$this->domain;
            $mail->Body     = $this->emailBody();
            $mail->IsHtml(true);
            $mail->Send();
            return TRUE;
        } catch (phpmailerException $e) {
            echo $e->errorMessage(); //Errors from PHP-Mailer
        } catch (Exception $e) {
            echo $e->getMessage(); //General Errors        
        }
    }

    private function emailBody(){
        return  '
                <html>
                    <body>
                        <table>
                            '.$this->emailContent().'
                        </table>
                    </body>
                </html>
                ';
    }

    private function emailContent(){
        $html = "";
        foreach ($this->values as $name => $value) {
            $html .= "
                        <tr>
                            <td>{$name}:</td>
                            <td>{$value}</td>
                        </tr>
                     ";
        }
        return $html;
    }
}
?>
