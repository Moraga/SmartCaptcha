<?php
/**
 * PHP Simple Captcha
 *
 * Questions:
 * - Which are vowels?
 * - Which are consonants?
 * - Which are (black|red|green|blue|*)?
 *
 * Requires:
 * - php gd
 *
 * Example:
 *
 * $captcha = new SmartCaptcha;
 * $_SESSION['answer'] = $captcha->create();
 * $captcha->output();
 *
 * @author Alejandro Fernandez Moraga <moraga86@gmail.com>
 */

// Captcha text font
define('SMARTCAPTCHA_FONT1', __DIR__ .'/Arial_Black.ttf');

// Captcha question font
define('SMARTCAPTCHA_FONT2', __DIR__ .'/Arial.ttf');

class SmartCaptcha {
	/**
	 * Captcha image
	 * @var resource
	 */
	private $image;
	
	/**
	 * Background color of the Captcha
	 * @var array
	 */
	private $backgroundColor = array(233, 234, 235);
	
	/**
	 * Characters used in the composition of the Captcha
	 * @var Array
	 */
	private $chars = array(
		'vowels' => 
			array('A', 'E', 'I', 'O', 'U'),
		
		'consonants' =>
			array('B', 'C', 'D', 'F', 'G', 'H', 'J', 
					'K', 'L', 'M', 'N', 'P', 'Q', 'R', 
					'S', 'T', 'V', 'W', 'X', 'Y', 'Z')
	);
	
	/**
	 * Character color options
	 * @var array
	 */
	private $colors = array(
		'black'	=> array(0, 0, 0),
		'red'	=> array(221, 0, 0),
		'green'	=> array(47, 79, 47),
		'blue'	=> array(50, 50, 205)
	);
	
	/**
	 * Get the Captcha image
	 * @return resource
	 */
	function image() {
		return $this->image;
	}
	
	/**
	 * Set the background color of the Captcha
	 * @param int $red Value of red component
	 * @param int $green Value of green component
	 * @param int $blue Value of blue component
	 * @return void
	 */
	function setBackgroundColor($red, $green, $blue) {
		$this->backgroundColor = array($red, $green, $blue);
	}
	
	/**
	 * Adds a character color option
	 * @param string $name The name of the color
	 * @param int $red Value of red component
	 * @param int $green Value of green component
	 * @param int $blue Value of green component
	 * @return void
	 */
	function setColor($name, $red, $green, $blue) {
		$this->colors[$name] = array($red, $green, $blue);
	}
	
	/**
	 * Removes a character color option
	 * @param string $name The name of the color
	 * @return void
	 */
	function removeColor($name) {
		unset($this->colors[$name]);
	}
	
	/**
	 * Creates a Captcha
	 * @param int $characteres The number of characteres
	 * @return string Returns the answer of the Captcha
	 */
	function create($characters=5) {
		$pos_ini = 20;
		$chr_len = 35;

		$charst = array_keys($this->chars);
		$colors = array_keys($this->colors);

		// creates the image
		$this->image = imagecreate($characters * $chr_len + $pos_ini * 2, 80);
		
		// sets the background color
		imagecolorallocate($this->image, $this->backgroundColor[0], $this->backgroundColor[1], $this->backgroundColor[2]);

		// container of questions and answers
		$avail = array();

		for ($i=0; $i < $characters; $i++) {
			// choose a character type: vowel or consonant
			$chart = $charst[rand(0, 1)];
			
			// get a character by previous type chosen
			$c = $this->chars[$chart][rand(0, count($this->chars[$chart]) - 1)];
			
			// adds the character as an answer for type
			if (empty($avail[$chart]))
				$avail[$chart] = $c;
			else
				$avail[$chart] .= $c;
			
			// choose a color for the character
			$color = $colors[rand(0, count($this->colors) - 1)];
			
			// adds the character as an answer for color
			if (empty($avail[$color]))
				$avail[$color] = $c;
			else
				$avail[$color] .= $c;

			// writes the character in the image
			imagettftext(
				// image
				$this->image,
				// character width
				30,
				// inclination
				rand(0, 25) * (rand(0, 1) ? 1 : -1),
				// x-axis position
				$chr_len * $i + $pos_ini,
				// y-axis position
				45,
				// character color
				imagecolorallocate($this->image, $this->colors[$color][0], $this->colors[$color][1], $this->colors[$color][2]), SMARTCAPTCHA_FONT1, $c);
		}
		
		// available questions
		$questions = array_keys($avail);
		
		// choose a question
		$question = rand(0, count($avail) - 1);
		
		// writes the question in the image
		imagettftext($this->image, 10.5, 0, 5, 72, imagecolorallocate($this->image, 0, 0, 0), SMARTCAPTCHA_FONT2, 'Which are '. $questions[$question] .'?');
		
		// returns the answer
		return $avail[$questions[$question]];
	}
	
	/**
	 * Outputs the Captcha as a PNG image
	 * @return void
	 */
	function output() {
		header('Pragma: no-cache');
		header('Cache-Control: private, no-cache, no-cache="Set-Cookie", proxy-revalidate');
		header('Content-type: image/png');
		imagepng($this->image);
		imagedestroy($this->image);
	}
}

?>