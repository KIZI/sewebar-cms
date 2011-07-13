/**                                                      
 * Function: clone_obj
 * It makes deep copy of object.
 *
 * Parameters:
 * obj     {Object} Object to be cloned
 *
 * Returns:
 * {Object} Clone of old object
 */
function clone_obj(obj) { 
    if (typeof obj !== 'object' || obj == null) {
        return obj;
    }

    var c = obj instanceof Array ? [] : {};
    
    for (var i in obj) {
        var prop = obj[i];

        if (typeof prop == 'object') {
           if (prop instanceof Array) {
               c[i] = [];

               for (var j = 0; j < prop.length; j++) {
                   if (typeof prop[j] != 'object') {
                       c[i].push(prop[j]);
                   } else {
                       c[i].push(clone_obj(prop[j]));
                   }
               }
           } else {
               c[i] = clone_obj(prop);
           }
        } else {
           c[i] = prop;
        }
    }

    return c;
}

/**
 * Class: UtilsAR
 * Standard Utils class encapsulating functionalities of creating some partsof HTML
 */
var UtilsAR = new Class({
    /**
     * Function: createDiv
     * It creates HTMLElement and returns it
     *
     * Parameters:
     * id           {String} Id
     *
     * Returns:
     * {HTMLElement} created HTMLElement
     */
    createDiv: function(id){
        return new Element('div',{
            id: id
        });
    },

    /**
     * Function: createDivClass
     * It creates HTMLElement and returns it
     *
     * Parameters:
     * clas     {String} class
     *
     * Returns:
     * {HTMLElement} created HTMLElement
     */
    createDivClas: function(clas){
        return new Element('div',{
            'class': clas
        });
    },

    /**
     * Function: createDivIdClas
     * It creates HTMLElement and returns it
     *
     * Parameters:
     * id           {String} id
     * clas        {String} class
     *
     * Returns:
     * {HTMLElement} created HTMLElement
     */
    createDivIdClas: function(id, clas){
        return new Element('div',{
            'class': clas,
            id: id
        });
    },

    /**
     * Function: createDivHtmlClass
     * It creates HTMLElement and returns it
     *
     * Parameters:
     * html     {String} html
     * clas        {String} class
     *
     * Returns:
     * {HTMLElement} created HTMLElement
     */
    createDivHtmlClas: function(html, clas){
        return new Element('div',{
            'class': clas,
            html: html
        });
    },
    
    createDivIdHtmlClas: function(id, html, clas){
        return new Element('div',{
            'class': clas,
            id: id,
            html: html
        });
    },
    
    createDivIdHtml: function(id, html){
        return new Element('div',{
            id: id,
            html: html
        });
    },
    
    createLabel: function(text){
    	return new Element('label', {
    		html: text
    	});
    },
    
    createInputText: function(id, value){
    	return new Element('input', {
    		type: 'text',
    		id: id,
    		value: value
    	});
    	
    },
    
    createInputSubmit: function(id, value){
    	return new Element('input', {
    		type: 'submit',
    		id: id,
    		value: value
    	});
    	
    },

    /**
     * Function: createHtmlIdClick
     * It creates HTMLElement and returns it
     *
     * Parameters:
     * id     {String} id
     * html    {String} html
     * click           {String} click
     *
     * Returns:
     * {HTMLElement} created HTMLElement
     */
    createHtmlIdClick: function(id, html, click){
        return new Element('div',{
            id: id,
            html: html,
            onclick: click
        });
    },

    /**
     * Function: createARElement
     * It creates new Element that should be droppable on.
     *
     * Parameters:
     * classs     {String} class
     * html    {String} html
     * ruleNumber           {String} ruleNumebr
     * elementNumber    {String} elementNumber
     * type     {String} type
     *
     * Returns:
     * {HTMLElement} ARElement
     */
    createARElement: function(classs,html,ruleNumber, elementNumber, type){
        return new Element('div',{
            'class': classs,
            html: html,
            ruleposition: ruleNumber,
            elementposition: elementNumber,
            type: type
        })
    },

    /**
     * Function: createPrvek
     * It creates new Element that should be draggable.
     *
     * Parameters:
     * classs     {String} class
     * type           {String} type
     * html    {String} html
     * state    {String} state(0 or 1 and it it is necessary ofr snapping function)
     * nameb     {String} nameb(Basic name necessary for serialization)
     *
     * Returns:
     * {HTMLElement} ARElement
     */
    createPrvek: function(classs, elementName){
        var prvek = new Element('div', {
            'class': classs,
            html: elementName
        });
        prvek.correctPlace = false;
        prvek.shouldBeCreated = false;
        prvek.rulePosition = null;
        return prvek;
    }
})

