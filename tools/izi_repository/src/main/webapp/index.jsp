<%@page contentType="text/html" pageEncoding="UTF-8"%>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    	               "http://www.w3.org/TR/html4/loose.dtd">

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>IZI Repository - Part of SEWEBAR project</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <style>
    	body{
			font-family: sans-serif;
			margin: 20px;
		}
		i{
			margin-left: 10px;
			color: gray;
			font-size: 10pt;
		}
		h1{
			color: #4682B4;
		}
		input, select{
			padding: 1px 1px 1px 5px;
			border-width: 1px;
			border-color: black;
			border-style: solid;
			font-family: sans-serif;
		}
		select {
			min-width: 300px;
		}
		textarea{
			padding: 2px 5px 2px 5px;
			border-width: 1px;
			border-color: black;
			border-style: solid;
			font-family: Consolas, Monospaced, serif;
			font-size: 9pt;
		}
		#send_btn{
			font-weight: bold;
		}
		.mainpage .right {
			text-align: right;
		}
		.mainpage .left {
		  	text-align: left;
		}
    </style>
    <script type="text/javascript">
    	function showField(selected){
	        if (selected == "getDocsNames" || selected == "getQueriesNames" || selected == "completeTest" || selected == "listIn" || selected == "getDescription" || selected == "removeAllDocuments" || selected == "actDescription") {
	            window.document.getElementById("id").style.display = "none";
	            window.document.getElementById("content").style.display = "none";
	            window.document.getElementById("docName").style.display = "none";
	            window.document.getElementById("creationTime").style.display = "none";
	            window.document.getElementById("reportUri").style.display = "none";
	            window.document.getElementById("restructure").style.display = "none";
	        } else if (selected == "directQuery10" || selected == "addIndex" || selected == "delIndex" || selected == "addDocumentMultiple" || selected == "jaxpQuery") {
	            window.document.getElementById("id").style.display = "none";
	            window.document.getElementById("docName").style.display = "none";
	            window.document.getElementById("creationTime").style.display = "none";
	            window.document.getElementById("content").style.display = "table-row";
	            window.document.getElementById("reportUri").style.display = "none";
	            window.document.getElementById("restructure").style.display = "none";
	        } else if (selected == "getQuery" || selected == "deleteQuery" || selected == "getDocument" || selected == "deleteDocument") {
	            window.document.getElementById("id").style.display = "table-row";
	            window.document.getElementById("content").style.display = "none";
	            window.document.getElementById("docName").style.display = "none";
	            window.document.getElementById("creationTime").style.display = "none";
	            window.document.getElementById("reportUri").style.display = "none";
	            window.document.getElementById("restructure").style.display = "none";
	        } else if (selected == "addDocument") {
	            window.document.getElementById("id").style.display = "table-row";
	            window.document.getElementById("content").style.display = "table-row";
	            window.document.getElementById("docName").style.display = "table-row";
	            window.document.getElementById("creationTime").style.display = "table-row";
	            window.document.getElementById("reportUri").style.display = "table-row";
	            window.document.getElementById("restructure").style.display = "none";
	        } else if (selected == "directQuery") {
	            window.document.getElementById("id").style.display = "none";
	            window.document.getElementById("docName").style.display = "none";
	            window.document.getElementById("creationTime").style.display = "none";
	            window.document.getElementById("content").style.display = "table-row";
	            window.document.getElementById("reportUri").style.display = "none";
	            window.document.getElementById("restructure").style.display = "table-row";
	        } else if (selected == "useQuery") {
	            window.document.getElementById("id").style.display = "table-row";
	            window.document.getElementById("docName").style.display = "none";
	            window.document.getElementById("creationTime").style.display = "none";
	            window.document.getElementById("content").style.display = "table-row";
	            window.document.getElementById("reportUri").style.display = "none";
	            window.document.getElementById("restructure").style.display = "table-row";
	        } else {
	            window.document.getElementById("id").style.display = "table-row";
	            window.document.getElementById("content").style.display = "table-row";
	            window.document.getElementById("docName").style.display = "none";
	            window.document.getElementById("creationTime").style.display = "none";
	            window.document.getElementById("reportUri").style.display = "none";
	            window.document.getElementById("restructure").style.display = "none";
	        }
	    }
    </script>
  </head>
  <script type="text/javascript" src="script.js"></script>
  <body  onLoad="showField(document.formular.action.value)">
    <h1>IZI Repository - Part of SEWEBAR project</h1>
    <h2>Search PMML documents stored in Berkeley XML DB</h2>
    <table class="mainpage">
    <form action="xquery_servlet" method="post" name="formular" target="_blank">
    <tr style="display: table-row;"><td class="right"><b>Action: </b></td><td class="left">
    <select name="action" onChange="showField(this.value)">
      <option value="useQuery">Use query</option>
      <option value="directQuery">Direct query</option>
<!--       <option value="directQuery10">Direct query 10x</option> -->
<!--       <option value="addQuery">Add query</option> -->
<!--       <option value="getQuery">Show query</option> -->
<!--       <option value="deleteQuery">Delete query</option> -->
<!--       <option value="getQueriesNames">Show saved XQuery names</option> -->
      <option value="getDocsNames">Show saved document names</option>
      <option value="addDocument">Add document</option>
<!--       <option value="addDocumentMultiple">Add documents [multiple]</option> -->
      <option value="getDocument">Show document</option>
      <option value="deleteDocument">Delete document</option>
      <option value="addIndex">Add index</option>
      <option value="delIndex">Remove index</option>
      <option value="listIndexes">Show indexes</option>
      <option value="actDescription">Update DataDescription</option>
      <option value="getDescription">Show DataDescription</option>
<!--       <option value="completeTest">Test settings</option> -->
<!--       <option value="removeAllDocuments">!!! Remove all documents !!!</option> -->
      <%--<option value="jaxpQuery">JAXP Query test</option>--%>
      <%--<option value="existQuery">Exist-DB XQuery</option>--%>
    </select></td></tr>
    <tr id="id"><td class="right">
    <b>Doc ID/Query ID: </b></td><td class="left"><input type="text" name="id" id="id" size="75">
    <i>Help: Field for query name/document name</i>
    </td></tr>
    <tr id="docName"><td class="right">
    <b>Doc Name: </b></td><td class="left"><input type="text" name="docName" id="docName" size="75">
    <i>Help: Field for document name</i>
    </td></tr>
    <tr id="creationTime"><td class="right">
    <b>Creation Time: </b></td><td class="left"><input type="text" name="creationTime" id="creationTime" size="75" value="<%= new java.util.Date() %>">
    <i>Help: Field for document creation time</i>
    </td></tr>
    <tr id="reportUri"><td class="right">
    <b>Report URI: </b></td><td class="left"><input type="text" name="reportUri" id="reportUri" size="75">
    <i>Help: Field for report URI</i>
    </td></tr>
    <tr id="restructure"><td class="right"><b>Change result structure (PMML like)</b></td><td class="left"><input type="checkbox" name="restructure" value="true"></td></tr>
    <tr id="content"><td class="right">
    <b>Doc content/Query content:</b></td><td class="left"><br />
    <textarea name="content" rows="20%" cols="120%" id="content"></textarea>
    <br /><i>Help: Field for query body/document body/index</i>
    </td></tr>
    <tr style="display: table-row;"><td class="right">&nbsp;</td><td class="left"><input type="submit" id="send_btn" value="SEND REQUEST"></td></tr>
    </form>
    </table>
    <br />
    <br />
    <i>Version 2.0.0 (x.2012)</i>
    <hr />
    <form name="settingsForm" action="xquery_servlet" method="post" target="_blank">
    	<input type="hidden" name="action" value="showsettings">
        <a href="javascript:document.settingsForm.submit()">Show/edit settings</a>
    </form>
  </body>
</html>