<?php
class JuceneAnalyzer extends Zend_Search_Lucene_Analysis_Analyzer_Common

{

	private $_position;



	/**

	* Reset token stream

	*/

	public function reset()

	{

		$this->_position = 0;

	}



	/**

	* Tokenization stream API

	* Get next token

	* Returns null at the end of stream

	*

	* @return Zend_Search_Lucene_Analysis_Token|null

	*/

	public function nextToken()

	{

		if ($this->_input === null) {

			return null;

		}



		while ($this->_position < strlen($this->_input)) {

			// skip white space

			while ($this->_position < strlen($this->_input) &&

			!ctype_alnum( $this->_input[$this->_position] )) {

				$this->_position++;

			}



			$termStartPosition = $this->_position;



			// read token

			while ($this->_position < strlen($this->_input) &&

			ctype_alnum( $this->_input[$this->_position] )) {

				$this->_position++;

			}



			// Empty token, end of stream.

			if ($this->_position == $termStartPosition) {

				return null;

			}



			$token = new Zend_Search_Lucene_Analysis_Token(

			substr($this->_input,

			$termStartPosition,

			$this->_position -

			$termStartPosition),

			$termStartPosition,

			$this->_position);

			$token = $this->normalize($token);

			if ($token !== null) {

				return $token;

			}

			// Continue if token is skipped

		}



		return null;

	}

}



?>