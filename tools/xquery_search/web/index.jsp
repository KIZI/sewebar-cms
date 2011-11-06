<%@page contentType="text/html" pageEncoding="UTF-8"%>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    	               "http://www.w3.org/TR/html4/loose.dtd">

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Vyhledávání v Berkeley XML DB</title>
    <style type="text/css" >
      body{
        font-family: Arial, sans-serif;
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
        font-family: Arial;
      }
      textarea{
      	padding: 2px 5px 2px 5px;
      	border-width: 1px;
        border-color: black;
        border-style: solid;
        font-family: Monospaced, serif;
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
  </head>
  <script type="text/javascript">
      function zobrazPole(vybrano){
          if (vybrano == "getDocsNames" || vybrano == "getQueriesNames" || vybrano == "completeTest" || vybrano == "listIn" || vybrano == "getDescription" || vybrano == "removeAllDocuments" || vybrano == "actDescription") {
              window.document.getElementById("id").style.display = "none";
              window.document.getElementById("content").style.display = "none";
              window.document.getElementById("docName").style.display = "none";
              window.document.getElementById("creationTime").style.display = "none";
              window.document.getElementById("reportUri").style.display = "none";
              window.document.getElementById("restructure").style.display = "none";
          } else if (vybrano == "directQuery10" || vybrano == "addIndex" || vybrano == "delIndex" || vybrano == "addDocumentMultiple" || vybrano == "jaxpQuery") {
              window.document.getElementById("id").style.display = "none";
              window.document.getElementById("docName").style.display = "none";
              window.document.getElementById("creationTime").style.display = "none";
              window.document.getElementById("content").style.display = "table-row";
              window.document.getElementById("reportUri").style.display = "none";
              window.document.getElementById("restructure").style.display = "none";
          } else if (vybrano == "getQuery" || vybrano == "deleteQuery" || vybrano == "getDocument" || vybrano == "deleteDocument") {
              window.document.getElementById("id").style.display = "table-row";
              window.document.getElementById("content").style.display = "none";
              window.document.getElementById("docName").style.display = "none";
              window.document.getElementById("creationTime").style.display = "none";
              window.document.getElementById("reportUri").style.display = "none";
              window.document.getElementById("restructure").style.display = "none";
          } else if (vybrano == "addDocument") {
              window.document.getElementById("id").style.display = "table-row";
              window.document.getElementById("content").style.display = "table-row";
              window.document.getElementById("docName").style.display = "table-row";
              window.document.getElementById("creationTime").style.display = "table-row";
              window.document.getElementById("reportUri").style.display = "table-row";
              window.document.getElementById("restructure").style.display = "none";
          } else if (vybrano == "directQuery") {
              window.document.getElementById("id").style.display = "none";
              window.document.getElementById("docName").style.display = "none";
              window.document.getElementById("creationTime").style.display = "none";
              window.document.getElementById("content").style.display = "table-row";
              window.document.getElementById("reportUri").style.display = "none";
              window.document.getElementById("restructure").style.display = "table-row";
          } else if (vybrano == "useQuery") {
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
  <body  onLoad="zobrazPole(document.formular.action.value)">
    <h1>Vyhledávání v Berkeley XML DB</h1>
    <table class="mainpage">
    <form action="xquery_servlet" method="post" name="formular" target="_blank">
    <tr style="display: table-row;"><td class="right"><b>Výběr akce: </b></td><td class="left">
    <select name="action" onChange="zobrazPole(this.value)">
      <option value="useQuery">Použít query</option>
      <option value="directQuery">Přímé zadání query</option>
      <option value="directQuery10">Přímé zadání query 10x</option>
      <option value="addQuery">Přidat query</option>
      <option value="getQuery">Zobrazit query</option>
      <option value="deleteQuery">Smazat query</option>
      <option value="getQueriesNames">Zobrazit názvy uložených XQuery</option>
      <option value="getDocsNames">Zobrazit názvy uložených dokumentů</option>
      <option value="addDocument">Přidat dokument</option>
      <option value="addDocumentMultiple">Přidat dokumenty [multiple]</option>
      <option value="getDocument">Zobrazit dokument</option>
      <option value="deleteDocument">Smazat dokument</option>
      <option value="addIndex">Přidat index</option>
      <option value="delIndex">Odstranit index</option>
      <option value="listIn">Zobrazit indexy</option>
      <option value="actDescription">Aktualizovat DataDescription</option>
      <option value="getDescription">Zobrazit DataDescription</option>
      <option value="completeTest">Otestování nastavení</option>
      <option value="removeAllDocuments">!!! Odstranit všechny dokumenty !!!</option>
      <%--<option value="jaxpQuery">JAXP Query test</option>--%>
      <%--<option value="existQuery">Exist-DB XQuery</option>--%>
    </select></td></tr>
    <tr id="id"><td class="right">
    <b>Doc ID/Query ID: </b></td><td class="left"><input type="text" name="id" id="id" size="75">
    <i>Nápověda: Pole slouží pro zadání názvu query nebo názvu dokumentu</i>
    </td></tr>
    <tr id="docName"><td class="right">
    <b>Doc Name: </b></td><td class="left"><input type="text" name="docName" id="docName" size="75">
    <i>Nápověda: Pole pro zadání názvu dokumentu</i>
    </td></tr>
    <tr id="creationTime"><td class="right">
    <b>Creation Time: </b></td><td class="left"><input type="text" name="creationTime" id="creationTime" size="75" value="<%= new java.util.Date() %>">
    <i>Nápověda: Pole slouží pro zadání času vytvoření dokumentu</i>
    </td></tr>
    <tr id="reportUri"><td class="right">
    <b>Report URI: </b></td><td class="left"><input type="text" name="reportUri" id="reportUri" size="75">
    <i>Nápověda: Pole slouží pro zadání URI adresy reportu</i>
    </td></tr>
    <tr id="restructure"><td class="right"><b>Změnit strukturu výsledku (PMML like)</b></td><td class="left"><input type="checkbox" name="restructure" value="true"></td></tr>
    <tr id="content"><td class="right">
    <b>Doc content/Query content:</b></td><td class="left"><br />
    <textarea name="content" rows="20%" cols="120%" id="content"></textarea>
    <br /><i>Nápověda: Pole slouží pro zadání těla query, těla dokumentu nebo indexu</i>
    </td></tr>
    <tr style="display: table-row;"><td class="right">&nbsp;</td><td class="left"><input type="submit" id="send_btn" value="PROVEĎ"></td></tr>
    </form>
    </table>
    <br />
    <br />
    <i>verze 1.19 (6.11.2011)</i>
    <hr />
    <form name="settingsForm" action="xquery_servlet" method="post" target="_blank">
    	<input type="hidden" name="action" value="showsettings">
        <%-- <input type="submit" value="Nastavení"> --%>
        <a href="javascript:document.settingsForm.submit()">Zobrazit nastavení</a>
    </form>
  </body>
</html>