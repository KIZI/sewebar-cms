/*
    Class:      Pager
    Author:     Brad Kellett
    Version:    1.0
    Date:       24/8/09
    Built For:  Mootools 1.2.3
    Website:    http://bradkellett.com/p/mootools-pager-class
*/

var Pager = new Class({
    Implements: [Options, Events],
    options: {
        innerElement: '.scroller',                // Selector for the element shifted for scrolling
        actuatorFormat: 'number',                 // The format for page links, can be 'number', 'alpha', 'none'
        showPrevNext: true,                       // Whether to show the previous and next elements
        prevSymbol: '<',                          // Symbol to use for the previous element
        nextSymbol: '>',                          // Symbol to use for the next element
        pageLimit: 0,                             // Maximum number of pages, 0 is unlimited
        actuatorType: 'span',                     // DOM Element type to use for actuators
        transition: Fx.Transitions.Cubic.easeOut, // Transition to use when scrolling
        duration: 500,                             // Duration for scrolling animation
        viewport_height: 290
        //onScroll: $empty                        // Event fired when user scrolls to a page
    },
    
    alphabet: "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
    
    initialize: function(container, pager, options) {
        this.setOptions(options);
        this.container = $(container);
        this.pager = $(pager);
        this.container.setStyle('overflow-y', 'hidden');
        this.content = this.container.getElement(this.options.innerElement);
        this.content.set('tween', {transition: this.options.transition, duration: this.options.duration});

        this.current_page = 0;
        this.viewport_height = this.options.viewport_height;
        var content_height = $$('#found-rules ul li').length * 29 - 2;
        this.total_pages = Math.ceil(content_height / this.viewport_height);
        if (this.options.pageLimit > 0 && total_pages > this.options.pageLimit)
            total_pages = this.options.pageLimit;
        
        this.createActuators();
    },
    
    createActuators: function () {
        this.pager.empty();
        if (this.total_pages > 1) {
            if(this.options.showPrevNext) {
                this.pager.grab(new Element(this.options.actuatorType, {
                    'class': 'pager-prev inactive',
                    text: this.options.prevSymbol,
                    events: {
                        click: function(e) {
                            this.gotoPage(e);
                        }.bind(this)
                    }
                }).store('page', 'prev'));
            }
            
            var actuator_marker;
            for(i = 0; i < this.total_pages; i++) {
                if(this.options.actuatorFormat == 'number')
                    actuator_marker = i+1;
                else if (this.options.actuatorFormat == 'alpha' && this.total_pages < 26)
                    actuator_marker = this.alphabet.charAt(i);
                else
                    actuator_marker = ''
                
                this.pager.grab(new Element(this.options.actuatorType, {
                    'class': 'pager-actuator',
                    text: actuator_marker,
                    events: {
                        click: function(e) {
                            this.gotoPage(e);
                        }.bind(this)
                    }
                }).store('page', i));
            }
            
            if(this.options.showPrevNext) {
                this.pager.grab(new Element(this.options.actuatorType, {
                    'class': 'pager-next inactive',
                    text: this.options.nextSymbol,
                    events: {
                        click: function(e) {
                            this.gotoPage(e);
                        }.bind(this)
                    }
                }).store('page', 'next'));
            }
            
            this.pager.getElements('.pager-actuator')[this.current_page].addClass('active');
            this.updateArrows();
        }
    },
    
    updateActuators: function () {
		// calculate number of pages
		var content_height = this.content.getSize().y - 2;
		this.total_pages = Math.ceil(content_height / this.viewport_height);
		
		// go to last page
		if (this.current_page >= this.total_pages) {
			//this.current_page = this.total_pages - 1;
			this.gotoPage('prev');
			this.createActuators();
		} else {
			this.createActuators();
		}
	},
    
    updateArrows: function() {
        if(this.options.showPrevNext) {
            if(this.current_page > 0)
                this.pager.getElement('.pager-prev').removeClass('inactive').addClass('active');
            else
                this.pager.getElement('.pager-prev').removeClass('active').addClass('inactive');
            
            if(this.current_page == this.total_pages - 1)
                this.pager.getElement('.pager-next').removeClass('active').addClass('inactive');
            else
                this.pager.getElement('.pager-next').removeClass('inactive').addClass('active');
        }
    },
    
    gotoPage: function(locator) {
    	if (typeof locator === 'object') {
    		actuator = $(locator.target);
            var page_number = actuator.retrieve('page');
    	} else {
    		var page_number = locator;
    	}
        
        if (actuator.hasClass('pager-prev') && !actuator.hasClass('active'))
            return;
        else if (actuator.hasClass('pager-next') && !actuator.hasClass('active'))
            return;
        
        if (page_number == 'next') {
            page_number = this.current_page + 1;
        } else if (page_number == 'prev') {
            page_number = this.current_page - 1;
        }
        
        var scroll_to = (this.viewport_height * page_number);
        this.pager.getElements('.pager-actuator')[page_number].addClass('active').getSiblings('.pager-actuator').removeClass('active');
        this.content.tween('margin-top', "-" + scroll_to + "px");
        this.current_page = page_number;
        this.updateArrows();
        this.fireEvent('onScroll', this.current_page);
    }
    
});
