window.addEvent('domready', function(){
	var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&'), id_source = null;
	for(var i = 0; i < hashes.length; i++) {
		hash = hashes[i].split('=');
		if (hash[0] == "id_source") {
			id_source = hash[1];
			break;
		}
	}
	
    asocRule = new AsociationRules("en","testGetInfo.php","testSetInfo.php","testHitsInfoDemo.php?" + (id_source != null ? "id_source=" + id_source : "")); //third param is the page on server to be called at saving rules
    asocRule.addEvent('saved', function(data){
        if(window.opener){
            window.opener.getRules(data);
        }
        window.close();
    });


});

var JSONObject = new Class({

});