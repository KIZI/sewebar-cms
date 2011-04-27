window.addEvent('domready', function(){
    asocRule = new AsociationRules("en","testGetInfo.php","testSetInfo.php","testHitsInfo.php"); //third param is the page on server to be called at saving rules
    asocRule.addEvent('saved', function(data){
        if(window.opener){
            window.opener.getRules(data);
        }
        window.close();
    });


});

var JSONObject = new Class({

});