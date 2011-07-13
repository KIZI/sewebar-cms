/**                              
 * Class: AsociationRules
 * This is the main class of the Application AsociationRulesGUI. This class creates the
 * structure and manage communication between the other parts.
 */
var AsociationRules = new Class({
    Implements: Events,

    MAIN_DIV_ID: "mainDiv", 

    /**
     * Function: initialize
     * This function creates instance of this class. It sets lenguage of application
     * and locations of necessary resources on the server.
     *
     * Parameters:
     * lang         {String} Language of the application
     * urlGet       {String} Url where the app gets Info
     * urlSet       {String} Url where the app serializes Info
     * urlHits      {String} Url where the app gets hits
     */
    initialize: function(lang, urlGet, urlSet, urlHits){
    	// sources
    	this.sources = [];
    	
    	// urls
    	this.urlSet = urlSet;
        this.parseUrlHits(urlHits);
        
        // lang
        this.lang = lang;

        this.asociationRules = new Array();

        this.availableBooleans = new Array();
        this.availableAttributes = new Array();
        this.availableInterestMeasures = new Array()

        this.language = new LanguageSupport();
        
        // init TaskStates
        this.interruptedStates = ['Interrupted'];
        this.finishedStates = ['Solved'].combine(this.interruptedStates);
    	this.inProgressStates = ['Waiting', 'Running', 'Not Generated'].combine(this.interruptedStates);

    	// init hits display
    	this.maxNumHits = 2;
    	this.numHitsDisplayed = 0;
    	
        this.getInfo(urlGet);
    },
    
    getSourceById: function(id_source) {
    	for (i = 0; i< this.sources.length; i++) {
    		if (this.sources[i]["id"] == id_source) {
    			this.sources[i];
    		}
    	}
    	
    	return null;
    },
    
    getSourcesInProgress: function(id_source) {
    	if (id_source != null) {
    		var source = this.getSourceById(id_source);
    		return source["inProgress"];
    	}
    	
    	for (i = 0; i< this.sources.length; i++) {
    		if (this.sources[i]["inProgress"] == true) {
    			return true;
    		}
    	}
    	
    	return false;
    },

    parseUrlHits: function(url) {
    	var idSource = this.getUrlVar(url, "id_source");
    	if (idSource != null) {
    		if (idSource.indexOf(',') != -1) {
    			var ids = idSource.split(',');
    			for (i = 0; i < ids.length; i++) {
    				this.sources[i] = [];
    				this.sources[i]["id"] = ids[i].toInt();
        			this.sources[i]["inProgress"] = false;
    			}
    		} else {
    			this.sources[0] = [];
    			this.sources[0]["id"] = idSource.toInt();
    			this.sources[0]["inProgress"] = false;
    		}
    	}
    	
    	this.urlHits = url.slice(0, url.indexOf("id_source") + 10);
    },
    
    getUrlVar: function(url, name) {
    	var vars = this.getUrlVars(url);
    	if (vars.indexOf(name) != -1) {
    		return vars[name];
    	}
    	
    	return null;
    },
    
    getUrlVars: function(url) {
    	var vars = [], hash;
    	var hashes = url.slice(url.indexOf('?') + 1).split('&');
    	
    	for(var i = 0; i < hashes.length; i++) {
    		hash = hashes[i].split('=');
    		vars.push(hash[0]);
    		vars[hash[0]] = hash[1];
    	}

    	return vars;
    },
    
    /**
     * Function: getInfo
     * This function gets Data and Configuration from server and solve the JSON
     * it gets.
     *
     * Parameters:
     * url     {String} url on the web where the app gets info.
     */
    getInfo: function(url){
        new Request.JSON({
            url: url,
            onComplete: function(item){
                this.lang = item.lang;
                // optional - nynější stav, prohibited - nezobrazí se displayAskingWindow, required - "" není funkční řešení."
                AsociationRules.imThreshold = item.imThreshold;
                AsociationRules.attrCoef = item.attrCoef;
                LanguageSupport.actualLang = this.lang;

                this.serverInfo = new ServerInfo(item)
                var moreRules = this.serverInfo.getMoreRules();
                if(moreRules == "false"){
                    moreRules = false;
                }
                else{
                    moreRules = true;
                }
                new BasicStructureGUI(this.serverInfo.getBooleans(), this.serverInfo.getAttributes(), this.serverInfo.getOperators(), this.MAIN_DIV_ID, this.lang, moreRules, this.maxNumHits, this.sources);

                if(moreRules){
                    $("newRule").addEvent('click', function(event){
                        var newRule = new AsociationRule(this.serverInfo);
                        newRule.addEvent("display", function(){
                            this.setDraggability();
                        }.bind(this));
                        newRule.setMaxSize(this.maxSize);
                        this.asociationRules.push(newRule);
                        var newDiv = newRule.display();
                        newDiv.inject($('rightDivPlace'));
                        this.drag.removeDragability();
                        this.drag.createDragability();
                    }.bind(this));
                }

                $("saveRule").addEvent('click', function(event){
                    var wholeJson = new JSONHelp();
                    var rule = null;
                    for(var actualRule = 0; actualRule < this.asociationRules.length; actualRule++){
                        rule = this.asociationRules[actualRule].toJSON();
                        if(rule == null){
                            new Hlaseni(this.language.getName(this.language.INCORRECT_RULE, this.lang));
                            return;
                        }
                        wholeJson["rule"+actualRule] = this.asociationRules[actualRule].toJSON();
                    }
                    wholeJson.rules = actualRule;
                    var jsonString = JSON.encode(wholeJson);
                    $$('.rule').each(function(ele){
                        ele.dispose();
                    });
                    this.saveServer(jsonString);
                }.bind(this));
                
                $("getHits").addEvent('click', function(event){
                	var wholeJson = new JSONHelp();
                	this.maxNumHits = $('limitHitsInput').value;
                	wholeJson.limitHits = this.maxNumHits;
                    var rule = null;
                    for(var actualRule = 0; actualRule < this.asociationRules.length; actualRule++){
                        rule = this.asociationRules[actualRule].toJSON();
                        if(rule == null){
                        	$('ruleLabel').innerHTML = this.language.getName(this.language.RULE_STATE_INCOMPLETE, this.lang);
                        	return;
                        }
                        wholeJson["rule"+actualRule] = this.asociationRules[actualRule].toJSON();
                    }
                    wholeJson.rules = actualRule;
                    var jsonString = JSON.encode(wholeJson);
                    
                    $('ruleLabel').innerHTML = this.language.getName(this.language.RULE_STATE_COMPLETE, this.lang);
                	
                    // call server and get hits
                    this.numHitsDisplayed = 0;
                    
                    // get hits for each source
                    for (i = 0; i < this.sources.length; i++) {
                    	this.sources[i]["inProgress"] = true;
                    	this.getHits(this.sources[i]["id"], jsonString, 0);                    	
                    }
                }.bind(this));
                
                this.maxSize = this.solveSize();
                var allRules = this.serverInfo.getExistingRules();
                for(var actualRule = 0; actualRule < allRules.length; actualRule++){
                    allRules[actualRule].setMaxSize(this.maxSize);
                    allRules[actualRule].addEvent("display", function(){
                        this.setDraggability();
                    }.bind(this));
                    var newRuleDiv1 = allRules[actualRule].display();
                    newRuleDiv1.inject($('rightDivPlace'));
                }
                this.asociationRules = this.asociationRules.concat(allRules);
                
                if(!moreRules && this.asociationRules.length < 1){
                    var newAsociationRule = new AsociationRule(this.serverInfo);
                    newAsociationRule.addEvent("display", function(){
                        this.setDraggability();
                    }.bind(this));
                    this.asociationRules.push(newAsociationRule);
                    var newRuleDiv = newAsociationRule.display();
                    // This should be injected into the left part.
                    newRuleDiv.inject($('rightDivPlace'));
                }

                this.drag = new Dragability(".ARElement",".prvek");
                this.setDraggability();
            }.bind(this)
        }).get();
    },

    /**
     * Function: solveSize
     * It gets max Size of element
     *
     * Returns:
     * {Number} max size
     */
    solveSize: function(){
        // Solve the size of ARElement based on the biggest size.
        var maxSize = 0;
        $$(".prvek").each(function(el){
            var actualSize = el.getSize().y;
            if(actualSize > maxSize){
                maxSize = actualSize;
            }
        });

        return maxSize;
    },

    /**
     * Function: setDraggability
     * This function sets draggability of elements which should be dragged.
     */
    setDraggability: function(){
        this.drag.removeDragability();
        this.drag.createDragability();
    },

    /**
     * Function: saveServer
     * This function is called by save() and it actually sends the data on the
     * server in variable data.
     *
     * Parameters:
     * which  {String} Data that should be sent to the server.
     */
    saveServer: function(which){
        new Request({
            url: this.urlSet,
            onComplete: function(item){
                this.asociationRules = new Array();
                var hlaseni = new Hlaseni(this.language.getName(this.language.EVERYTHING_OK, this.lang));
                hlaseni.addEvent('closehlaseni', function() {
                    this.fireEvent('saved', item);
                }.bind(this));
            }.bind(this)
        }).post({
            'data': which
        });
    },
    
    /**
     * Function: getHits
     * This function is called to get hits for the active association rule
     *
     * Parameters:
     * url    {String} URL to be called.
     * which  {String} Data that should be sent to the server.
     */
    getHits: function(id_source, which, numAlreadyFound){
    	url = this.urlHits + id_source;
    	
    	if (this.numHitsDisplayed != 0 && this.getSourcesInProgress(null)) {
    		$('hitsLabel').innerHTML = this.language.getName(this.language.HITS_LABEL_LOADING_IMG, this.lang)+' '+this.language.getName(this.language.HITS_LABEL_FOUND, this.lang)+this.numHitsFound+' '+this.language.getName(this.language.HITS_LABEL_LOADING, this.lang);
    	} else {
    		$('hitsLabel').innerHTML = this.language.getName(this.language.HITS_LABEL_LOADING_IMG, this.lang)+' '+this.language.getName(this.language.HITS_LABEL_LOADING, this.lang);
    	}
    	
        new Request.JSON({
            url: url,
            onComplete: function(item){
            	this.serverInfo.solveHits(id_source, item);
            	this.numHitsDisplayed = this.numHitsDisplayed + this.serverInfo.countHits(id_source) - numAlreadyFound;
            	this.serverInfo.solveTaskState(item);
            	
            	var taskState = this.serverInfo.getTaskState();
            	console.log('TaskState: ' + taskState);

            	if (this.interruptedStates.indexOf(taskState) != -1 && this.numHitsDisplayed == this.maxNumHits) {
            		// mining is finished, it has reached the specified limit
            		this.updateHits(id_source, true);
            		this.hitsInProgress = false;
            	} else if (this.finishedStates.indexOf(taskState) != -1) {
            		// mining is finished
            		this.updateHits(id_source, false);
            		this.hitsInProgress = false;
            	} else if (this.inProgressStates.indexOf(taskState) != -1) {
            		// mining is in progress
            		this.updateHits(id_source, false);
            		this.hitsInProgress = true;
            		this.getHits(id_source, which, this.serverInfo.countHits(id_source));
            	} else {
            		// TODO new state?
            		this.hitsInProgress = false;
            	}
            	
            	
            }.bind(this)
        }).post({'data': which});
    },
    
    /**
     * Function: updateHits
     * This function is called to repaint hits for the active association rule
     */
    updateHits: function(id_source, limitReached) {
    	this.clearHits(id_source);
    	var hits = this.serverInfo.getHits(id_source);
    	
    	if (hits.length != 0) {
    		$('sourceLabel'+id_source).show('inline');
    	} else {
    		$('sourceLabel'+id_source).hide();
    	}
    	
    	for(var actualRule = 0; actualRule < hits.length; actualRule++){
    		hit = hits[actualRule];
    		hit.setMaxSize(this.maxSize / 2);
    		hit.addEvent("display", function(){
    			// TODO resolve draggability
                //this.setDraggability();
            }.bind(this));
    	    var newRuleDiv = hit.display();
    	    newRuleDiv.inject($('sourceHits'+id_source));
        }	
    	
    	$('limitHitsSubmit').show('inline');
    	
    	if (limitReached) {
    		$('hitsLabel').innerHTML = this.language.getName(this.language.HITS_LABEL_FOUND, this.lang)+this.numHitsDisplayed + ' ' + this.language.getName(this.language.HITS_LIMIT_REACHED, this.lang);
    	} else {
    		$('hitsLabel').innerHTML = this.language.getName(this.language.HITS_LABEL_FOUND, this.lang)+this.numHitsDisplayed;	
    	}
    },
    
    /**
     * Function: clearHits
     * This function is called to clear hits
     * 
     * TODO params doc
     */
    clearHits: function(id_source){
    	$('sourceHits' + id_source).empty();
    }
    
});

/**
 * Class: JSONHelp
 * This is supportive class for serialization into JSON. Basically it does nothing.
 */
var JSONHelp = new Class({

    });

function Counter(){
    if ( typeof Counter.counter == 'undefined' ) {
        // It has not... perform the initilization
        Counter.counter = 0;
    }
}
Counter.counter = 0;
