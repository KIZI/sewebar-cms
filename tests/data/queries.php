<?php
class QueriesData
{
	public static function getData()
	{
		return array(
			array(
				"query" => array(
					"query" => "using o for i\"http:\/\/psi.ontopedia.net\/\"\n o:composed_by(\$OPERA : o:Work, o:*Author* : o:Composer)?",
					"xslt" => "",
					"delimiter" => "*",
					"parameters" => array(
						"Author" => "Puccini"
					),
				),
				"result" => "using o for i\"http:\/\/psi.ontopedia.net\/\"\n o:composed_by(\$OPERA : o:Work, o:Puccini : o:Composer)?"
			),
			array(
				"query" => array(
					"query" => file_get_contents(dirname(__FILE__) . '/arbuilder.xquery'),
					"xslt" => "",
					"parameters" => NULL,
				),
				"result" => file_get_contents(dirname(__FILE__) . '/arbuilder_result.xquery'),
			),
		);
	}
}
?>
