<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<%@ page contentType="text/html" pageEncoding="UTF-8" %>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    	               "http://www.w3.org/TR/html4/loose.dtd">

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>XQuery search (Part of SEWEBAR project) - search PMML documents stored in Berkeley XML DB</title>
<%--     <c:url value="style.css" /> --%>
<%-- ${pageContext.request.contextPath} --%>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
  </head>
  <script type="text/javascript" src="<c:url value="js/script.js" />"></script>
  <body  onLoad="showField(document.formular.action.value)">
    <h1>XQuery search (Part of SEWEBAR project) - search PMML documents stored in Berkeley XML DB</h1>
    <table class="mainpage">
    <form action="<c:url value="/xquery_servlet" />" method="post" name="formular" target="_blank">
    <tr style="display: table-row;"><td class="right"><b>Action: </b></td><td class="left">
    <select name="action" onChange="showField(this.value)">
      <option value="useQuery">Use query</option>
      <option value="directQuery">Direct query</option>
      <option value="directQuery10">Direct query 10x</option>
      <option value="addQuery">Add query</option>
      <option value="getQuery">Show query</option>
      <option value="deleteQuery">Delete query</option>
      <option value="getQueriesNames">Show saved XQuery names</option>
      <option value="getDocsNames">Show saved document names</option>
      <option value="addDocument">Add document</option>
      <option value="addDocumentMultiple">Add documents [multiple]</option>
      <option value="getDocument">Show document</option>
      <option value="deleteDocument">Delete document</option>
      <option value="addIndex">Add index</option>
      <option value="delIndex">Remove index</option>
      <option value="listIn">Show indexes</option>
      <option value="actDescription">Update DataDescription</option>
      <option value="getDescription">Show DataDescription</option>
      <option value="completeTest">Test settings</option>
      <option value="removeAllDocuments">!!! Remove all documents !!!</option>
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
    <i>Version 1.22 (6.1.2012)</i>
    <hr />
    <form name="settingsForm" action="xquery_servlet" method="post" target="_blank">
    	<input type="hidden" name="action" value="showsettings">
        <a href="javascript:document.settingsForm.submit()">Show/edit settings</a>
    </form>
  </body>
</html>