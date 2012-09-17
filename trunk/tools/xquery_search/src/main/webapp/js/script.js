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