<?php

	namespace MVCFrame\Foundation;

use Transliterator;

	class Utility {

		public const CASE_DEFAULT 	= 0;
		public const CASE_LOWER 	= 1;
		public const CASE_UPPER 	= 2;

		protected static ?Utility $instance=NULL;

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		public static function getInstance(){
			// Check if already declared
			if(is_null(self::$instance)){
				self::$instance = new Utility();
			}

			// Return instance
			return self::$instance;
		}

		/**-------------------------------------------------------------------------*/
		/**
		 * Sanitizes a string and replaces any characters other than A-Za-z
		 * - Translates to Latin-ascii
		 * - Strips all other characters
		 * - Alters case if indicated
		 *
		 * @param string $input
		 * @param [type] $case
		 * @return void
		 */
		/**-------------------------------------------------------------------------*/
		public static function sanitizeLetters(string $input, $case=self::CASE_DEFAULT){
			// Set Default Pattern
			$pattern = '';

			// Translate
			if(class_exists("Translator")){
				$translator = Transliterator::createFromRules(':: Any-Latin; :: Latin-ASCII;');
				$input = $translator->transliterate($input);
			}

			// Trim
			$input = trim($input);

			// Determine Case
			switch($case){
				/**
				 * Case 2
				 * Upper
				 */
				case 2: 
					$input 	 = strtoupper($input);
					$pattern = '/[^A-Z]/';
					break;
				/**
				 * Case 1
				 * Lower
				 */
				case 1:
						$input 	 = strtolower($input);
						$pattern = '/[^a-z]/';
					break;
				/**
				 * Case 0
				 * Default
				 */
				case 0:
				default: 
					$pattern = '/[^a-zA-Z]/';
					break;
			}

			// Apply pattern
			$input = preg_replace($pattern, '', $input);

			// Return string
			return $input;
		}

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
		
		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/

		/**-------------------------------------------------------------------------*/
		/**-------------------------------------------------------------------------*/
	}
?>