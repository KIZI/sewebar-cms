function selectTable(tableName){
  document.getElementById('db_table_'+tableName).checked=true;
  document.getElementById('select_table_form').submit();
}
function selectPrimaryKey(column){
  document.getElementById('db_primary_key_'+column).checked=true;
  document.getElementById('select_primary_key_form').submit();
}


function enableSubmitPrimary(){ 
  checked=false;
  $$('input.primaryKeyRadio').each(function(input) {
    if (input.checked){
      checked=true;
    }
  });     
  if (checked){
    submit=$('dbSubmitButton');
    submit.disabled=false;
    submit.removeClass('disabled');
  }else{
    submit=$('dbSubmitButton');
    submit.disabled=true;
    submit.addClass('disabled');
  }
}

function enableSubmitTable(){ 
  checked=false;
  $$('input.tableRadio').each(function(input) {
    if (input.checked){
      checked=true;
    }
  });
  if (checked){
    submit=$('dbSubmitButton');
    submit.disabled=false;
    submit.removeClass('disabled');
  }else{
    submit=$('dbSubmitButton');
    submit.disabled=true;
    submit.addClass('disabled');
  }
}

window.addEvent('domready', function() {       
  primaryKey=false;
  table=false;
  $$('input.primaryKeyRadio').each(function(input) {
    input.addEvent('change',enableSubmitPrimary);
    primaryKey=true;
  });   
  if (primaryKey){
    enableSubmitPrimary();
  }else{      
    $$('input.tableRadio').each(function(input) {
      input.addEvent('change',enableSubmitTable);
      table=true;
    });  
    if (table){
      enableSubmitTable();
    }
  }
  
  
});