var Kalkulacka = new Class({
    Implements: Events,

    HODNOTA_KALK: "hodnota",
    KALKULACKA: "kalkulacka",
    KALKULACKA_CELA: "kalkulackaCela",
    hodnotaDispleje: 0,
    parametry: null,
    decimal: true,
    
    initialize: function(decimal, parametry){
        this.decimal = decimal;
        this.parametry = parametry;
    },

    /*
     * Doplní do stylu pro kalkulačku standardní hodnoty.
     */
    createCalculatorDefault: function(){
        var prepravkaStyl = new Prepravka();
        prepravkaStyl.vloz("position","absolute");
        prepravkaStyl.vloz("top","100px");
        prepravkaStyl.vloz("left","200px");
        prepravkaStyl.vloz("display","");
        prepravkaStyl.vloz("background-color","#efebe7");
        prepravkaStyl.vloz("z-index","10");
        prepravkaStyl.vloz("border","solid 4px black");
        return prepravkaStyl;
    },

    /*
     * Vytvoří, zobrazí a vynuluje kalkulačku.
     *
     * @param text Text, který se má ukázat v záhlaví kalkulačky
     */
    createCalculator: function(text, objekt){
        var elementTv = new TvorbaElementu();
        var prepravkaStyl = new Prepravka();
        var prepravkaAtribut = new Prepravka();

        this.hodnotaDispleje = 0;
        elementTv.vytvorMeziVrstvu();
        if(this.parametry == null){
            prepravkaStyl = this.createCalculatorDefault();
        }
        else{
            prepravkaStyl = this.parametry;
        }

        prepravkaAtribut.vloz("id",this.KALKULACKA_CELA);
        if(this.decimal){
            prepravkaAtribut.vloz('html', "<div id=\"celkovy\"><div id=\"nadpis\">"+text+"</div><div id=\"kalkulacka\"><table><tr><td colspan=\"3\"><div id=\"hodnota\">0</div></td></tr><tr><td><input type=\"button\" value=\"1\"/></td><td><input type=\"button\" value=\"2\"/></td><td><input type=\"button\" value=\"3\"/></td></tr><tr><td><input type=\"button\" value=\"4\" /></td><td><input type=\"button\" value=\"5\" /></td><td><input type=\"button\" value=\"6\" /></td></tr><tr><td><input type=\"button\" value=\"7\" /></td><td><input type=\"button\" value=\"8\" /></td><td><input type=\"button\" value=\"9\" /></td></tr><tr><td><input type=\"button\" value=\"0\" /></td><td><input type=\"button\" value=\".\" /></td><td><input type=\"button\" value=\"Clr\" onClick=\"pokladna.kalkulacka.vynuluj();\" /></td></tr><tr><td colspan=\"3\"><input type=\"button\" id=\"btOdesli\" value=\"OK\" /></td></tr></table></div></div>");
        }
        else{
            prepravkaAtribut.vloz('html', "<div id=\"celkovy\"><div id=\"nadpis\">"+text+"</div><div id=\"kalkulacka\"><table><tr><td colspan=\"3\"><div id=\"hodnota\">0</div></td></tr><tr><td><input type=\"button\" value=\"1\"/></td><td><input type=\"button\" value=\"2\"/></td><td><input type=\"button\" value=\"3\"/></td></tr><tr><td><input type=\"button\" value=\"4\" /></td><td><input type=\"button\" value=\"5\" /></td><td><input type=\"button\" value=\"6\" /></td></tr><tr><td><input type=\"button\" value=\"7\" /></td><td><input type=\"button\" value=\"8\" /></td><td><input type=\"button\" value=\"9\" /></td></tr><tr><td><input type=\"button\" value=\"0\" /></td><td><input type=\"button\" value=\"\" /></td><td><input type=\"button\" value=\"Clr\" onClick=\"pokladna.kalkulacka.vynuluj();\" /></td></tr><tr><td colspan=\"3\"><input type=\"button\" id=\"btOdesli\" value=\"OK\" /></td></tr></table></div></div>");
        }

        elementTv.vytvorDiv(prepravkaStyl, prepravkaAtribut, document.getElementsByTagName("body")[0]);

        this.pridejEventy(objekt);
        $('btOdesli').addEvent("click", function(){
            this.destroyCalculator();
        }.bind(this));
        this.fireEvent('opencalc');
    },

    /*
     * Všechna číselná tlačítka dostanou onClick = pridejHodnotu(this.value)
     */
    pridejEventy: function(objekt){
        var vsechnyButtony = $(this.KALKULACKA).getElements('input');
        vsechnyButtony.each(function(tlacitko){
            var porovnani = new Porovnavani();
            if(porovnani.jeKalkZnak(tlacitko.get("value"))){
                tlacitko.addEvent('click', function(event){
                    hodnota = event.target.get('value') + "";
                    this.pridejHodnotu(hodnota);
                }.bind(this));
            }
        }, this);
    },

    /*
     * Vynuluje hodnotu na kalkulačce
     */
    vynuluj: function(){
        this.hodnotaDispleje = 0;
        $(this.HODNOTA_KALK).set('html', this.hodnotaDispleje);
    },

    /*
     * Zruší kalkulačku a dá to vědět okolí.
     */
    destroyCalculator: function(){
        $(this.KALKULACKA_CELA).parentNode.removeChild($(this.KALKULACKA_CELA));
        $('podklad').parentNode.removeChild($('podklad'));
        this.fireEvent('closecalc');
    },

    /*
     *Vrátí aktuální hodnotu displeje
     */
    getHodnota: function(){
        return this.hodnotaDispleje;
    },

    /*
     * Na základě stisknutého tlačítka v rámci kalkulačky upraví číslo na displeji
     * kalkulačky.
     */
    pridejHodnotu: function(hodnota, objekt){
        if(this.hodnotaDispleje == 0){
            this.hodnotaDispleje = hodnota;
            $(this.HODNOTA_KALK).set('html', this.hodnotaDispleje);
        }
        else{
            this.hodnotaDispleje = this.hodnotaDispleje.concat(hodnota);
            $(this.HODNOTA_KALK).set('html', this.hodnotaDispleje);
        }
    }
});

