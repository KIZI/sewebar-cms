/**
 * Class: Dragability
 * This class ensures Dragability on choosen elements.
 */
var Dragability = new Class({
    /**
     * Function: initialize
     * It creates instance of class Dragability
     *
     * Parameters:
     * mainClass     {AsociationRules} main class of the application
     * droppa        {String} class of elements to be dropped on
     * dragga        {String} class pof element that can be dragged.
     */
    initialize: function(mainClass, droppa, dragga){
        this.asociationRules = mainClass;
        this.draggable = new Array();
        this.htmlCreation = new HTMLCreation();
        this.droppa = droppa;
        this.dragga = dragga;
    },

    /**
     * Function: isBoolean
     * It solves if the type is boolean
     *
     * Parameters:
     * type     {String} "and", "or", ...
     *
     * Returns:
     * {Boolean} 
     */
    isBoolean: function(type){
        if(type != "and" && type != "or" && type != "neg" && type != "lbrac" && type != "rbrac"){
            return false;
        }
        else{
            return true;
        }
    },

    /**
     * Function: dropFunction
     * It solves whether the element is dropped on correct place
     *
     * Parameters:
     * element     {HTMLElement} Element that is dropped.
     * dropedOn    {HTMLElement} Element to be dropped on.
     * event           {Event}
     */
    dropFunction: function(element, dropedOn, event){
        if(dropedOn == null){
            return;
        }
        if(this.asociationRules.canAdd(element, dropedOn)){
            element.dispose();
            dropedOn.empty();

            element.inject(dropedOn);
            element.set("style","");
            element.set("correctplace","yes");
            element.nameLang = element.get('html');
            if(!this.isBoolean(element.get("type"))){
                element.addEvent("click", function(){
                    asocRule.solveAskingWindow(this);
                })
            }

            this.solveCreatingNewPlace(dropedOn, element);
        }
        else{
            element.dispose();
        }
    },

    /**
     * Function: solveCreatingNewPlace
     * If the element is droped on the free place I must create new place at the
     * end of rule
     *
     * Parameters:
     * element {HTMLElement} element
     * dropedOn  {HTMLElement} element it is dropped on.
     */
    solveCreatingNewPlace: function(dropedOn, element){
        var necesaryCreate = true;
        if(dropedOn.get("type") != "free"){
            necesaryCreate = false;
        }
        dropedOn.set('type',element.get('type'));
        if(necesaryCreate){
            this.htmlCreation.createNewPlace(dropedOn.get("ruleposition"),dropedOn.get("elementposition") + 1);
        }
        
    },

    /**
     * Function: completeFunction
     * This function is called when the element is dropped. It looks if the element
     * is on the right place and if it isn't. It is removed.
     *
     * Parameters:
     * element     {HTMLElement} dropped element.
     */
    completeFunction: function(element){
        if(element.get("correctplace") != "yes"){
            if(element.get("ruleposition") != undefined){
                this.asociationRules.removeItem(element);
            }
            element.dispose();
        }
        else{
            element.set("correctplace","no");
        }

        this.removeDragability();
        this.createDragability();
    },

    /**
     * Function: snapFunction
     * It creates a copy of element that is being dragged. And say that there should
     * be no more copies of this element.
     * 
     * Parameters:
     * element     {HTMLElement} The element that should be copied.
     */
    snapFunction: function(element){
        if(element.get('state') == 0){
            var newElement = element.clone().inject($(element),'after');
            newElement.set('state',0);
            newElement.set('nameb',element.get('nameb'));
            newElement.set('correctplace','no');
            newElement.set('type', element.get('type'));
            element.set('state',1);
        }
    },

    /**
     * Function: createDragability
     * This function is called quite often and it adds to all elements, that
     * should be draggable, dragability. It also makes each of the places to be
     * able to receive tha draggable element.
     */
    createDragability: function(){
        //var drops = $$('.ARElement');
        //$$('.prvek').each(function(prvek){
        var drops = $$(this.droppa);
        $$(this.dragga).each(function(prvek){
            this.draggable.push(
            new Drag.Move($(prvek),{
                droppables: drops,

                onDrop: function(element, dropedOn, event) {
                    this.dropFunction(element, dropedOn, event);
                }.bind(this),

                onComplete: function(element, event){
                    this.completeFunction(element);
                }.bind(this),

                onSnap: function(element){
                    this.snapFunction(element);
                }.bind(this)
            }));
            
        }.bind(this));
    },

    /**
     * Function: removeDragability
     * It is necessary to remove ability to be draged and to be draged to in order
     * to achieve ability to be draged to on new places.
     */
    removeDragability: function(){
        var delkaPole = this.draggable.length;
        for(var i = 0; i < delkaPole; i++){
            this.draggable[i].detach();
        }
        this.draggable = new Array();
    }
})
