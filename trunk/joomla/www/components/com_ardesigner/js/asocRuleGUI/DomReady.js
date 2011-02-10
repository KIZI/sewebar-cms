window.addEvent('domready', function(){
    /*if(console == undefined){
        function console1()
        {
            this.log = function(param){
            //alert(param);
            }
        }

        //console = new console1();
    }*/

    Element.Properties.type = {
        get: function(){
            return this.type;
        },

        set: function(value){
            this.type = value;
            this.setAttribute('type', value);
        }

    };

    Element.Properties.state = {
        get: function(){
            return this.state;
        },

        set: function(value){
            this.state = value;
            this.setAttribute('state', value);
        }

    };

    Element.Properties.nameb = {
        get: function(){
            return this.nameb;
        },

        set: function(value){
            this.nameb = value;
            this.setAttribute('nameb', value);
        }

    };

    Element.Properties.valuesb = {
        get: function(){
            return this.valuesb;
        },

        set: function(value){
            this.valuesb = value;
            this.setAttribute('valuesb', value);
        }

    };

    Element.Properties.ruleposition = {
        get: function(){
            return this.ruleposition;
        },

        set: function(value){
            this.ruleposition = value;
            this.setAttribute('ruleposition', value);
        }

    };

    Element.Properties.elementposition = {
        get: function(){
            return this.elementposition;
        },

        set: function(value){
            this.elementposition = value;
            this.setAttribute('elementposition', value);
        }

    };

    Element.Properties.correctplace = {
        get: function(){
            return this.correctplace;
        },

        set: function(value){
            this.correctplace = value;
            this.setAttribute('correctplace', value);
        }

    };
	
    function gup( name )
    {
      name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
      var regexS = "[\\?&]"+name+"=([^&#]*)";
      var regex = new RegExp( regexS );
      var results = regex.exec( window.location.href );
      if( results == null )
        return "";
      else
        return results[1];
    }
        
    fileGetInfo = 'index.php?option=com_ardesigner&controller=features&format=raw&id_query=' + gup("id_query");
    fileSetInfo = 'index.php?option=com_ardesigner&controller=ardesigner&format=raw'; 
	
    asocRule = new AsociationRules("en", fileGetInfo, fileSetInfo); //third param is the page on server to be called at saving rules
    asocRule.addEvent('saved', function(data){
        if(window.opener){
            window.opener.getRules(data);
        }
        window.close();
    });


});