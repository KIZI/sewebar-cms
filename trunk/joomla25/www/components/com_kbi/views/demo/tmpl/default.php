<?php defined('_JEXEC') or die('Restricted access'); ?>

<h1>KBI debug console</h1>
<form action="<?php print $this->url?>" method="post">
	<label for="source">Source<span>(id or JSON)</span></label>
	<textarea id="source" name="source" rows="10" cols="80" style="width:100%;resize: vertical;">
{
    "url": "http:\/\/nlp.vse.cz:8081\/xquery_search\/xquery_servlet",
    "type": "XQUERY",
    "method": "POST",
    "params": {
        "variable": "query_test1",
        "action": "useQuery"
    }
}
	</textarea>
	<input type="button" value="Get Data Description" onclick="return KbiDataDictionary('<?php print $this->urlDataDictionary ?>');"/>

	<label>Source Examples</label>
	<pre>
{
    "type": "ONTOPIA",
    "url": "http:\/\/nlp.vse.cz:8080\/tmrap\/tmrap\/get-tolog",
    "topicmap": "ItalianOpera.ltm",
    "syntax": "text\/x-tmxml"
}
		</pre>
		<pre>
{
    "type": "XQUERY",
    "url": "http:\/\/nlp.vse.cz:8081\/xquery_search\/xquery_servlet\/"
}
		</pre>
		<pre>
{
    "type": "JUCENE",
    "url": "url nezacinajici http:// znamena lokalni instalaci jucene"
}
	</pre>
	<fieldset style="border: 1px solid gray; padding: 0px 10px 10px;">
		<legend>Build Query</legend>
		<label for="query">Query<span>(id or source specific language or JSON)</span></label>
		<textarea id="query" name="query" rows="10" cols="80" wrap="off" style="width:100%;resize: vertical;"></textarea>
		<label for="query">Query parameters<span>(JSON, XML in case of <em>Template Query</em> (empty query text and defined XSLT))</span></label>
		<textarea id="params" name="params" rows="10" cols="80" wrap="off" style="width:100%;resize: vertical;"></textarea>
		<label for="xslt">XSLT for results<span>(id or XML/XSLT)</span></label>
		<textarea id="xslt" name="xslt" rows="10" cols="80" wrap="off" style="width:100%;resize: vertical;"></textarea>
		<br />
		<input type="button" value="AJAX GET" onclick="return KbiGetAjax('<?php print $this->url?>');" disabled="disabled"/>
		<input type="button" value="AJAX POST" onclick="return KbiPostAjax('<?php print $this->url?>');"/>
		<input type="submit" value="POST"/>
	</fieldset>
	<fieldset style="border: 1px solid gray; padding: 0px 10px 10px;">
		<legend>Upload document</legend>
		<label for="document">Document<span>(PMML/HTML)</span></label>
		<textarea id="document" name="document" rows="10" cols="80" wrap="off" style="width:100%;resize: vertical;"></textarea>

		<input type="button" value="Upload" onclick="return KbiUploadDocument('<?php print $this->urlUploadDocument ?>');"/>
	</fieldset>
</form>
<form action="" method="post">
	<fieldset>
		<legend>Results</legend>
		<div id="messages">&nbsp;</div>
		<input type="button" onclick="$('results').empty(); return false;" value="Reset results" />
		<textarea id="results" name="results" rows="50" cols="80" readonly="readonly" style="width:100%"><?php print isset($this->results) ? var_dump($this->results) : '' ?></textarea>
	</fieldset>
</form>