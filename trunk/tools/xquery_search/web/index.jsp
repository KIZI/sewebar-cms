<%@page contentType="text/html" pageEncoding="UTF-8"%>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    	               "http://www.w3.org/TR/html4/loose.dtd">

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Vyhledávání v Berkeley XML DB</title>
    <style type="text/css" >
      body{
        font-family: Tahoma, Arial;
        margin: 50px;
      }
      i{
        margin-left: 10px;
        color: gray;
        font-size: 10pt;
      }
      h1{
        color: #4682B4;
      }
      div{
        padding: 1px 1px 10px 1px;
      }
      input, select{
        padding: 1px 1px 1px 10px;
        border-width: 1px;
        border-color: black;
        border-style: solid;
        font-family: Arial;
        width: 300px;
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
    </style>
  </head>
  <script type="text/javascript">
      function zobrazPole(vybrano){
          if (vybrano == "getDocsNames" || vybrano == "getQueriesNames" || vybrano == "completeTest" || vybrano == "listIn" || vybrano == "getDescription" || vybrano == "removeAllDocuments") {
              window.document.getElementById("id").style.display = "none";
              window.document.getElementById("content").style.display = "none";
              window.document.getElementById("docName").style.display = "none";
              window.document.getElementById("creationTime").style.display = "none";
              window.document.getElementById("reportUri").style.display = "none";
          } else if (vybrano == "directQuery" || vybrano == "directQuery10" || vybrano == "addIndex" || vybrano == "delIndex" || vybrano == "addDocumentMultiple" || vybrano == "jaxpQuery") {
              window.document.getElementById("id").style.display = "none";
              window.document.getElementById("docName").style.display = "none";
              window.document.getElementById("creationTime").style.display = "none";
              window.document.getElementById("content").style.display = "block";
              window.document.getElementById("reportUri").style.display = "none";
          } else if (vybrano == "getQuery" || vybrano == "deleteQuery" || vybrano == "getDocument" || vybrano == "deleteDocument") {
              window.document.getElementById("id").style.display = "block";
              window.document.getElementById("content").style.display = "none";
              window.document.getElementById("docName").style.display = "none";
              window.document.getElementById("creationTime").style.display = "none";
              window.document.getElementById("reportUri").style.display = "none";
          } else if (vybrano == "addDocument") {
              window.document.getElementById("id").style.display = "block";
              window.document.getElementById("content").style.display = "block";
              window.document.getElementById("docName").style.display = "block";
              window.document.getElementById("creationTime").style.display = "block";
              window.document.getElementById("reportUri").style.display = "block";
          } else {
              window.document.getElementById("id").style.display = "block";
              window.document.getElementById("content").style.display = "block";
              window.document.getElementById("docName").style.display = "none";
              window.document.getElementById("creationTime").style.display = "none";
              window.document.getElementById("reportUri").style.display = "none";
          }
      }
  </script>
  <body  onLoad="zobrazPole(document.formular.action.value)">
    <h1>Vyhledávání v Berkeley XML DB</h1>
    <form action="xquery_servlet" method="post" name="formular" target="_blank">
    <b>Výběr akce: </b>
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
      <option value="getDescription">Zobrazit DataDescription</option>
      <option value="completeTest">Otestování nastavení</option>
      <option value="removeAllDocuments">!!! Odstranit všechny dokumenty !!!</option>
      <%--<option value="jaxpQuery">JAXP Query test</option>--%>
      <%--<option value="existQuery">Exist-DB XQuery</option>--%>
    </select>
    <br />
    <br />
    <div id="id">
    <b>Doc ID/Query ID: </b><input type="text" name="id" id="id" size="150">
    <br /><i>Nápověda: Pole slouží pro zadání názvu query nebo názvu dokumentu</i>
    </div>
    <div id="docName">
    <b>Doc Name: </b><input type="text" name="docName" id="docName" size="150">
    <br /><i>Nápověda: Pole pro zadání názvu dokumentu</i>
    </div>
    <div id="creationTime">
    <b>Creation Time: </b><input type="text" name="creationTime" id="creationTime" size="150" value="<%= new java.util.Date() %>">
    <br /><i>Nápověda: Pole slouží pro zadání času vytvoření dokumentu</i>
    </div>
    <div id="reportUri">
    <b>Report URI: </b><input type="text" name="reportUri" id="reportUri" size="150">
    <br /><i>Nápověda: Pole slouží pro zadání URI adresy reportu</i>
    </div>
    <div id="restructure">Změnit strukturu výsledku query (PMML like) <input type="checkbox" name="restructure" value="true"></div>
    <div id="content">
    <b>Doc content/Query content:</b>
    <br />
    <textarea name="content" rows="20%" cols="120%" id="content"></textarea>
    <br /><i>Nápověda: Pole slouží pro zadání těla query, těla dokumentu nebo indexu</i>
    </div>
    <br />
    <%--
    <div id="folder">
        <input type="text" name="folder">
    </div>
    <div id="files">
        <script type="text/javascript">
            for (i=0; i<=10; i++){
                document.write("<input type=\"file\"><br />");
            }
        </script>
    </div> --%>
    <input type="submit" id="send_btn" value="PROVEĎ">
    </form>
    <br />
    <br />
    <i>verze 1.14 (20.7.2011)</i>
    <hr />    
    <form name="settingsForm" action="xquery_servlet" method="post" target="_blank">
    	<input type="hidden" name="action" value="showsettings">
        <%-- <input type="submit" value="Nastavení"> --%>
        <a href="javascript:document.settingsForm.submit()">Zobrazit nastavení</a>
    </form>
  </body>
</html>