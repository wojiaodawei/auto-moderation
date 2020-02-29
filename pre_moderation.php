<?php
	
	class PreModeration {

		var $forbidden; 
		var $forbiddenStatements; 
		var $sensitive; 
		var $sensitiveStatements; 

		/**
		 * Constructor
		 *
		 */
		function __construct() {
			$this->forbidden = $this->loadForbiddenWords();
			$this->forbiddenStatements = $this->loadForbiddenStatements();
			$this->sensitive = $this->loadSensitiveWords();
			$this->sensitiveStatements = $this->loadSensitiveStatements();
		}

		/**
		 * Load forbidden **words** from interdits.csv file
		 *
		 * @return	array
		 */
		function loadForbiddenWords() { 
			$forbidden = array();
			if (($handle = fopen("restrictions/interdits.csv", "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$forbiddenWord = $this->normalizeString($data[0]);
					if(strpos(trim($forbiddenWord), ' ') === false) {
						// Add singular and plural forms
						array_push($forbidden, $forbiddenWord);
						array_push($forbidden, $forbiddenWord."s");
					} 
				}
				fclose($handle);
			}  
			return $forbidden;
 		}

		/**
		 * Load forbidden **statements** from interdits.csv file
		 *
		 * @return	array
		 */
		function loadForbiddenStatements() { 
			$forbidden = array();
			if (($handle = fopen("restrictions/interdits.csv", "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$forbiddenWord = $this->normalizeString($data[0]);
					if(strpos(trim($forbiddenWord), ' ') !== false) {
						array_push($forbidden, $forbiddenWord);
					} 
				}
				fclose($handle);
			}  
			return $forbidden;
 		}
 
		/**
		 * Load sensitive **words** from sensibles.csv file
		 *
		 * @return	array
		 */
   		function loadSensitiveWords() { 
			$sensitive = array();
			if (($handle = fopen("restrictions/sensibles.csv", "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
					$sensitiveWord = $this->normalizeString($data[0]);
					if(strpos(trim($sensitiveWord), ' ') === false) {
						// Add singular and plural forms
						$sensitive[$sensitiveWord] = $data[1];
						$sensitive[$sensitiveWord."s"] = $data[1];
					}
				}
				fclose($handle);
			}
			return $sensitive;
 		}

		/**
		 * Load sensitive **statements** from sensibles.csv file
		 *
		 * @return	array
		 */
   		function loadSensitiveStatements() { 
			$sensitive = array();
			if (($handle = fopen("restrictions/sensibles.csv", "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
					$sensitiveWord = $this->normalizeString($data[0]);
					if(strpos(trim($sensitiveWord), ' ') !== false) {
						$sensitive[$sensitiveWord] = $data[1];
					}
				}
				fclose($handle);
			}
			return $sensitive;
 		}

		/**
		 * Normalize the input string by stripping whitespaces, removing punctuation, making it lowercase and remove accented char
		 *
		 * @param	string	$string
		 * @return	string
		 */
		function normalizeString($string) { 
			// Strip whitespace
			$string = trim($string);
			// Remove punctuation from $string
			$string = preg_replace("#[[:punct:]]#", " ", $string);
			// Make the $string lowercase
			$string = mb_strtolower($string, 'UTF-8');
			// Remove accented UTF-8 characters from $string
			$search = explode(",","ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u");
			$replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u");
			$string = str_replace($search, $replace, $string);
			return $string;
 		}

		/**
		 * Check the input string validity
		 *
		 * @param	string	$text
		 * @return	integer 	-1 forbidden, 0 valid, 1/2/3 sensitive
		 */
		function validate($text){
			// Normalize first
			$text = $this->normalizeString($text); 
			// Split the string into an array
			$words = explode(" ", $text);

			// Check the validity word by word
			foreach ($words as &$word) {
				if (in_array($word, $this->forbidden)) { 
			  		// There is (at least) one forbidden word
					return -1;
			  	} else if (array_key_exists($word, $this->sensitive)) { 
			  		// There is (at least) one sensitive word
					$degree = $this->sensitive[$word];
					return $degree; //return 1, 2 or 3 according to its degree of sensitivity
			  	}
			}

			// Check the validity statement by statement
			foreach ($this->forbiddenStatements as &$forbiddenStatement) {
				if(strpos($text, $forbiddenStatement) !== false) {
					// There is (at least) one forbidden statement
					return -1;
				}
			}
			foreach ($this->sensitiveStatements as $sensitiveStatement => $degree) {
				if(strpos($text, $sensitiveStatement) !== false) {
					// There is (at least) one sensitive statement
					return $degree; //return 1, 2 or 3 according to its degree of sensitivity
				}
			}

			return 0; // the input string is valid
		}
	} 


	if(isset($_GET['query']) && $_GET['query'] != "" && isset($_GET['field']) && $_GET['field'] != "") {
		
		$value = $_GET['query'];
		$formfield = $_GET['field'];

		// CONST
		$SENSITIVE_CATEGORIES = array(
			1 => "insulte/racisme",
			2 => "homophobie/pornographie/sexisme",
			3 => "religion/politique"
		);

		$preModeration = new PreModeration();

		if ($formfield == "title" || $formfield == "message") {

			$validated = $preModeration->validate($value);

			if ($validated == 0) { 
		  		echo "<span>Valid</span>";
		  	} else if ($validated == -1) {
				echo "Présence de mot(s) interdit(s) !";
			} else {
				$sensitiveCategory = $SENSITIVE_CATEGORIES[$validated];
				echo "Présence de mot(s) à caractère ".$sensitiveCategory." !";
			}
		}
	}
?>
