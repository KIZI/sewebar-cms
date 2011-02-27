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
          if (vybrano == "getDocsNames" || vybrano == "getQueriesNames" || vybrano == "completeTest" || vybrano == "listIn") {
              window.document.getElementById("nazev").style.display = "none";
              window.document.getElementById("data").style.display = "none";
          } else if (vybrano == "directQuery" || vybrano == "directQuery10" || vybrano == "addIndex" || vybrano == "delIndex" || vybrano == "addDocumentMultiple") {
              window.document.getElementById("nazev").style.display = "none";
              window.document.getElementById("data").style.display = "block";
          } else if (vybrano == "getQuery" || vybrano == "deleteQuery" || vybrano == "getDocument" || vybrano == "deleteDocument") {
              window.document.getElementById("nazev").style.display = "block";
              window.document.getElementById("data").style.display = "none";
          } else {
              window.document.getElementById("nazev").style.display = "block";
              window.document.getElementById("data").style.display = "block";
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
      <%--<option value="directQuery10">Přímé zadání query 10x</option>--%>
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
      <option value="completeTest">Otestování nastavení</option>
      <%--<option value="existQuery">Exist-DB XQuery</option>--%>
    </select>
    <br />
    <br />
    <div id="nazev">
    <b>ID:&nbsp;&nbsp;&nbsp;</b><input type="text" name="variable" id="variable" size="150">
    <br /><i>Nápověda: Pole slouží pro zadání názvu query nebo názvu dokumentu</i>
    </div>
    <br />
    <div id="data">
    <b>Data:</b>
    <br />
    <textarea name="content" rows="20%" cols="120%" id="content"></textarea>
    <br /><i>Nápověda: Pole slouží pro zadání těla query, těla dokuentu nebo indexu</i>
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
    <i>verze 1.05 (26.2.2011)</i>
  </body>
</html>