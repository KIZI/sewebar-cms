/**                                                                
 * Class: Hits
 * The main purpose of this class is to get mining results from multiple sources
 */
var Hits = new Class({
	
	/**
	 * TODO func spec
	 * 
	 * TODO param spec
	 */
	initialize: function(associationRules, lang, language) {
		// sources, GUI, ServerInfo, lang, LanguageSupport
    	this.sources = [];
    	this.gui = null;
    	this.serverInfo = null;
    	this.lang = lang;
    	this.language = language;
		
    	// AssociationRules controller
		this.associationRules = associationRules;
		
		// init hit Request array
    	this.hitRequests = [];
    	
    	// init hits display
    	this.maxNumHits = 2;
    	this.numHitsDisplayed = 0;
    	
        // init TaskStates
        this.interruptedStates = ['Interrupted'];
        this.finishedStates = ['Solved'].combine(this.interruptedStates);
    	this.inProgressStates = ['Waiting', 'Running', 'Not Generated'].combine(this.interruptedStates);
	},
	
	/**
	 * TODO func spec
	 * 
	 * TODO param spec
	 */
	getMaxNumHits: function() {
		return this.maxNumHits;
	},
	
	/**
	 * TODO func spec
	 * 
	 * TODO param spec
	 */
	updateMaxNumHits: function() {
		this.maxNumHits = parseInt($('limitHitsInput').value);
		
		this.getMaxNumHits();
	},
	
	/**
	 * TODO func spec
	 * 
	 * TODO param spec
	 */
	getNumHitsDisplayed: function() {
		return this.numHitsDisplayed;
	},
	
	/**
	 * TODO func spec
	 * 
	 * TODO param spec
	 */
	getSourcesLength: function() {
		return this.sources.length;
	},
	
	/**
	 * TODO func spec
	 * 
	 * TODO param spec
	 */
	getSourceByPos: function(pos) {
		if (typeof this.sources[pos] !== 'undefined') {
			return this.sources[pos];
		}
		
		return null;
	},
	
	/**
	 * TODO func spec
	 * 
	 * TODO param spec
	 */
	parseSources: function(url) {
		var idSource = this.getUrlVar(url, "id_source");
		if (idSource != null) {
			if (idSource.indexOf(',') != -1) {
				var ids = idSource.split(',');
				for (i = 0; i < ids.length; i++) {
					this.sources[i] = [];
					this.sources[i]["id"] = ids[i].toInt();
				}
			} else {
				this.sources[0] = [];
				this.sources[0]["id"] = idSource.toInt();
			}
			this.urlHits = url.slice(0, url.indexOf("id_source") + 10);
			return;
		}
		
		this.urlHits = url;
	},
	
	/**
	 * TODO func spec
	 * 
	 * TODO param spec
	 */
    getUrlVar: function(url, name) {
    	var vars = this.getUrlVars(url);
    	if (vars.indexOf(name) != -1) {
    		return vars[name];
    	}
    	
    	return null;
    },
    
	/**
	 * TODO func spec
	 * 
	 * TODO param spec
	 */
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
	 * TODO func spec
	 * 
	 * TODO param spec
	 */
    getSourceById: function(id_source) {
    	for (i = 0; i < this.sources.length; i++) {
    		if (this.sources[i]["id"] == id_source) {
    			this.sources[i];
    		}
    	}
    	
    	return null;
    },
    
	/**
	 * TODO func spec
	 * 
	 * TODO param spec
	 */
    setGUI:function(gui) {
    	this.gui = gui;
    },
    
	/**
	 * TODO func spec
	 * 
	 * TODO param spec
	 */
    setServerInfo:function(serverInfo) {
    	this.serverInfo = serverInfo;
    },
    
	/**
	 * TODO func spec
	 * 
	 * TODO param spec
	 */
    getHits: function(json) {
    	this.reset();
    	
    	// get hits for each source
        for (i = 0; i < this.sources.length; i++) {
        	this.gui.clearHits(this.sources[i]["id"]);
        	this.getHitsRequest(this.sources[i]["id"], json, 0);                    	
        }
    },
    
    /**
	 * TODO func spec
	 * 
	 * TODO param spec
	 */
    reset: function() {
    	this.stopHitRequests();
    	this.numHitsDisplayed = 0;
		
		// init hit Request array - no need now
    	//this.hitRequests = [];
    },
    
	/**
	 * TODO func spec
	 * 
	 * TODO param spec
	 */
    handleErrorRequest: function() {
    	if (!this.hitsInProgress() && this.numHitsDisplayed < this.maxNumHits) {
    		this.gui.setHitsStatusLabel(this.language.getName(this.language.HITS_LABEL_FOUND, this.lang) + this.numHitsDisplayed + ' ' + this.language.getName(this.language.HITS_LABEL_ERROR, this.lang));
    		this.gui.showLimitHitsSubmit();
    	}
    },
    
    /**
     * Function: getHits
     * This function is called to get hits for the active association rule
     *
     * Parameters:
     * url    {String} URL to be called.
     * json  {String} Data that should be sent to the server.
     */
    getHitsRequest: function(id_source, json, numAlreadyFound){
    	var url = this.urlHits + id_source;
    	
    	if (this.numHitsDisplayed != 0 && this.hitsInProgress()) {
    		this.gui.setHitsStatusLabel(this.language.getName(this.language.HITS_LABEL_LOADING_IMG, this.lang)+' '+this.language.getName(this.language.HITS_LABEL_FOUND, this.lang)+this.numHitsFound+' '+this.language.getName(this.language.HITS_LABEL_LOADING, this.lang));
    	} else {
    		this.gui.setHitsStatusLabel(this.language.getName(this.language.HITS_LABEL_LOADING_IMG, this.lang)+' '+this.language.getName(this.language.HITS_LABEL_LOADING, this.lang));
    	}
    	
        var req = new Request.JSON({
            url: url,
            secure: true,
            
            onSuccess: function(responseJSON, responseText) {
            	this.serverInfo.solveHits(id_source, responseJSON);
            	this.serverInfo.solveTaskState(id_source, responseJSON);
            	
            	var limitExceeded = false;
            	if ((this.numHitsDisplayed - numAlreadyFound + this.serverInfo.countHits(id_source)) > this.maxNumHits) {
            		limitExceeded = true;
            		this.stopHitRequests();
            	} else {
            		searchLimit = this.maxNumHits - this.numHitsDisplayed - numAlreadyFound;
            	}
            	
            	if (this.interruptedStates.indexOf(this.serverInfo.getTaskState(id_source)) != -1 && this.serverInfo.countHits(id_source) == this.maxNumHits) {
            		// mining is finished, it has reached the specified limit
            		this.updateHits(id_source, true, numAlreadyFound);
            		// TODO stop?
            	} else if (this.finishedStates.indexOf(this.serverInfo.getTaskState(id_source)) != -1) {
            		// mining is finished
            		this.updateHits(id_source, limitExceeded, numAlreadyFound);
            		// TODO stop?
            	} else if (this.inProgressStates.indexOf(this.serverInfo.getTaskState(id_source)) != -1) {
            		// mining is in progress
            		this.updateHits(id_source, limitExceeded, numAlreadyFound);
            		if (limitExceeded) {
            			// TODO stop?
            		} else {
            			json.limitHits = searchLimit;
            			this.getHitsRequest(id_source, json, this.serverInfo.countHits(id_source));
            		}
            	} else {
            		// TODO new state?
            	}
            }.bind(this),
            
            onError: this.handleErrorRequest.bind(this),

            onCancel: this.handleErrorRequest.bind(this),
            
            onFailure: this.handleErrorRequest.bind(this),
            
            onException: this.handleErrorRequest.bind(this),
            
            onTimeout: this.handleErrorRequest.bind(this),

        }).post({'data': json});
        
        this.addHitRequest(req);
    },
    
    /**
     * Function: updateHits
     * This function is called to repaint hits for the active association rule
     */
    updateHits: function(id_source, limitExceeded, numAlreadyFound) {
    	this.gui.clearHits(id_source);
    	var hits = this.serverInfo.getHits(id_source);
    	
    	if (this.serverInfo.countHits(id_source) != 0) {
    		this.gui.showSourceLabel(id_source);
        	for(var i = 0; i < hits.length; i++) {
        		// do not display more rules than set limit
        		if ((this.numHitsDisplayed - numAlreadyFound + i) == this.maxNumHits) { break; }
        		
        		var hit = hits[i];
        		this.gui.displayHit(this, hit, id_source);
            }	
    	}
    	
    	// TODO when? every time?
    	this.gui.showLimitHitsSubmit();
    	this.numHitsDisplayed = Math.min(this.maxNumHits, this.numHitsDisplayed + this.serverInfo.countHits(id_source) - numAlreadyFound);
    	
    	if (limitExceeded) {
    		this.gui.setHitsStatusLabel(this.language.getName(this.language.HITS_LABEL_FOUND, this.lang)+this.numHitsDisplayed + ' ' + this.language.getName(this.language.HITS_LIMIT_REACHED, this.lang));
    	} else {
    		this.gui.setHitsStatusLabel(this.language.getName(this.language.HITS_LABEL_FOUND, this.lang)+this.numHitsDisplayed);
    	}

    },
	
    /**
     * Function: addHitRequest
     * TODO function spec
     * 
     * TODO params doc
     */
    addHitRequest: function(req){
    	this.hitRequests.push(req);
    },
    
	/**
	 * TODO func spec
	 * 
	 * TODO param spec
	 */
    hitsInProgress: function() {
    	for (i = 0; i < this.hitRequests.length; i++) {
    		if (this.hitRequests[i].isRunning()) {
    			return true;
    		}
    	}
    	
    	return false;
    },
    
    /**
     * TODO function spec
     * 
     * TODO params doc
     */
    stopHitRequests: function() {
    	Array.each(this.hitRequests, function(req, index){
    		if (req.isRunning()) {
    			try {
    				req.cancel();
    			} catch(err) {}
    		}
    	});
    }
    
});