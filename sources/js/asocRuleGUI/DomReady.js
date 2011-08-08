window.addEvent('domready', function(){
	params = window.location.href.slice(window.location.href.indexOf('?') + 1);
    asocRule = new AsociationRules("en","testGetInfo.php","testSetInfo.php","testHitsInfoDemo.php?" + params); //third param is the page on server to be called at saving rules
    asocRule.addEvent('saved', function(data){
        if(window.opener){
            window.opener.getRules(data);
        }
        window.close();
    });


});

var JSONObject = new Class({

});