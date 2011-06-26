/**                                                                  
 * Class: Dragability
 * This class ensures Dragability on choosen elements.
 */
var Dragability = new Class({
    Implements: [Events],

    /**
     * Function: initialize
     * It creates instance of class Dragability
     *
     * Parameters:
     * placesToDrop               {String} class of elements to be dropped on
     * elementsToBeDragged        {String} class pof element that can be dragged.
     */
    initialize: function(placesToDrop, elementsToBeDragged){
        this.draggableElements = new Array();
        this.placesToDrop = placesToDrop;
        this.elementsToBeDragged = elementsToBeDragged;
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
        // u rule divu budu mit atribut asociationRule
        var asociationRule = dropedOn.parentNode.asociationRule;
        var canAdd = false
        // Tohle není dobré odlišení
        var position = dropedOn.get("name").replace("rule", "");
        if(element.element.isInRule != undefined){
            if(element.element.isInRule){
                asociationRule.removeItem(element);
                asociationRule.insertItem(element.element, position)
            }
        }
        else{
            if(asociationRule.insertItem(element.element, position)){
                canAdd = true;
            }
        }
        if(!canAdd){
            element.dispose();
        }
        else{
            element.correctPlace = true;
            asociationRule.showAsking();
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
        if(!element.correctPlace){
            //It is necessary to remove element from the rule.
            if(element.parentNode != null && element.parentNode.parentNode != null &&
                element.parentNode.parentNode.asociationRule != null){
                var asociationRule = element.parentNode.parentNode.asociationRule;
                asociationRule.removeItem(element);
            }
            element.dispose();
        }
        else{
            if(element.parentNode != null && element.parentNode.parentNode != null &&
                element.parentNode.parentNode.asociationRule != null){
                var asociationRule1 = element.parentNode.parentNode.asociationRule;
                asociationRule1.removeItem(element);
            }
            element.element.isInRule = true;
            element.correctPlace = false;
            element.dispose();
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
        
    },

    /**
     * Function: createDragability
     * This function is called quite often and it adds to all elements, that
     * should be draggable, dragability. It also makes each of the places to be
     * able to receive tha draggable element.
     */
    createDragability: function(){
        var placesToDrop = $$(this.placesToDrop);
        $$(this.elementsToBeDragged).each(function(draggableEl){
            this.draggableElements.push(
                new Drag.Move($(draggableEl),{
                    droppables: placesToDrop,

                    onDrop: function(element, dropedOn, event) {
                        this.dropFunction(element, dropedOn, event);
                    }.bind(this),

                    onComplete: function(element, event){
                        this.completeFunction(element);
                    }.bind(this),

                    onSnap: function(element){
                        if(element.shouldBeCreated){
                            var newElement = element.clone().inject($(element),'after');
                            newElement.correctPlace = false;
                            newElement.shouldBeCreated = true;
                            newElement.rulePosition = null;
            
                            newElement.element = clone_obj(element.element);

                            element.shouldBeCreated = false;
                        }
                        else{
                            if(element.parentNode != null && element.parentNode.parentNode != null &&
                                element.parentNode.parentNode.asociationRule != null){
                                var position = element.parentNode.get("name").replace("rule", "");
                                var elements = element.parentNode.parentNode.asociationRule.elements;
                                if(position == elements.length-1){
                                    return;
                                }
                                for(var i = 0; i < elements.length; i++){
                                    if(elements[i] == null){
                                        this.stop();
                                        return;
                                    }
                                }
                            }
                        }
                    }
                }));

        }.bind(this));
    },

    /**
     * Function: removeDragability
     * It is necessary to remove ability to be draged and to be draged to in order
     * to achieve ability to be draged to on new places.
     */
    removeDragability: function(){
        for(var actualDraggable = 0; actualDraggable < this.draggableElements.length; actualDraggable++){
            this.draggableElements[actualDraggable].detach();
        }
        this.draggableElements = new Array();
    }
})
