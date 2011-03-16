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

window.addEvent('domready', function(){
    fileGetInfo = 'index.php?option=com_ardesigner&task=features&format=raw&id_query=' + gup("id_query");
    fileSetInfo = 'index.php?option=com_ardesigner&task=serialize&format=raw';

    asocRule = new AsociationRules("en", fileGetInfo, fileSetInfo); //third param is the page on server to be called at saving rules
    asocRule.addEvent('saved', function(data){
        if(window.opener){
            window.opener.getRules(data);
        }
        window.close();
    });


});

var JSONObject = new Class({

});