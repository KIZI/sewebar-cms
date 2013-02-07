  function showItemText(item){
    var elementId=item.id;
    elementId=elementId.substr(0,elementId.length-4)+"_type"; 
    if (item.checked){
      enableSubmitTask(true);
      var displayStyle='block';
    }else{
      var displayStyle='none';
      checkSubmitEnabled();
    }
    document.getElementById(elementId).style.display=displayStyle;
  }
  function iziShowItemText(item){
    var elementId=item.id;
    elementId=elementId.substr(0,elementId.length-4)+"_type"; 
    if (item.checked){
      var displayStyle='block';
    }else{
      var displayStyle='none';
    }
    document.getElementById(elementId).style.display=displayStyle;
  }
  function selectAllColumns(){
  	$$('input.columnCheckbox').each(function(input) {
      if (!input.disabled){
        input.checked = true;
      }
  	});
  	showSelection();
  }
  function selectNoneColumns(){
  	$$('input.columnCheckbox').each(function(input) {
  		input.checked = false;
  	});
  	showSelection();
  }
  
  function showSelection(){
    checked=false;
    $$('input.columnCheckbox').each(function(input) {
  		if (input.checked){
        displayStyle='block';
        checked=true;
      }else{
        displayStyle='none';
      }
      var elementId=input.id;
      elementId=elementId.substr(0,elementId.length-4)+"_type";
      document.getElementById(elementId).style.display=displayStyle;
      
  	});
    enableSubmitTask(checked);
  }
  
  /**
   *  Funkce pro kontrolu, jestli má být povolené odesílací tlačítko
   */     
  function checkSubmitEnabled(){
    checked=false;
    $$('input.columnCheckbox').each(function(input) {
  		if (input.checked){
        checked=true;
      }
  	});
    enableSubmitTask(checked);
  }
  
  function enableSubmitTask(enabled){ 
    submit=$('taskSubmitButton');
    if (!submit){return;}
    if (enabled){
      submit.disabled=false;
      submit.removeClass('disabled');
    }else{
      submit.disabled=true;
      submit.addClass('disabled');
    }
  }
  
  window.addEvent('domready', function() {
    showSelection();
  });