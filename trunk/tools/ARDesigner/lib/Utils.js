var Oznaceni = new Class({
    getSelText: function(){
        var txt = '';
        if (window.getSelection)
        {
            txt = window.getSelection();
        }
        else if (document.getSelection)
        {
            txt = document.getSelection();
        }
        else if (document.selection)
        {
            txt = document.selection.createRange().text;
        }
        else{
            return "";
        }
        return txt;
    }

})

var Tabulka = new Class({
    zacatek: 1,
    konec: 9,
    nazev: "TxtNazev",
    mnozstvi: "TxtMnozstvi",
    cena: "TxtCena",
    aktualniPosledniPozice: 0,
    predpona: "do_",
    pripona: "_text",

    initialize: function(zacatek1, konec1){
        this.zacatek = zacatek1;
        this.konec = konec1;
    },

    vyprazdni: function(){
        for(i = this.zacatek; i <= this.konec; i++){
            $(this.predpona+this.nazev+i+this.pripona).set('value',"");
            $(this.predpona+this.mnozstvi+i+this.pripona).set('value',"");
            $(this.predpona+this.cena+i+this.pripona).set('value',"");
        }
    },

    posunRadky: function(jeVolny){
        if(jeVolny){
            maximalniRadek = this.aktualniPosledniPozice;
        }
        else{
            maximalniRadek = this.konec-1;
        }
        for(i = maximalniRadek; i >= this.zacatek; i--){
            this.presunRadek(i);
        }
    },

    presunRadek: function(odkud){
        try{
            kam = odkud + 1;
            $(this.predpona+this.nazev+kam+this.pripona).set('value',$(this.predpona+this.nazev+odkud+this.pripona).get('value'));
            $(this.predpona+this.nazev+odkud+this.pripona).set('value',"");
            $(this.predpona+this.mnozstvi+kam+this.pripona).set('value',$(this.predpona+this.mnozstvi+odkud+this.pripona).get('value'));
            $(this.predpona+this.mnozstvi+odkud+this.pripona).set('value',"");
            $(this.predpona+this.cena+kam+this.pripona).set('value',$(this.predpona+this.cena+odkud+this.pripona).get('value'));
            $(this.predpona+this.cena+odkud+this.pripona).set('value',"");
        }
        catch(e){
            pokladna.zpracujHlaseni("Technick? chyba, kontaktujte pros?m technickou podporu.");
        }
    },

    vytvorRadek: function(nazev, mnozstvi, cena){
        try{
            porovnani = new Porovnavani();
            $(this.predpona+this.nazev+this.zacatek+this.pripona).set('value',nazev);
            mnozstvi = porovnani.zaokrouhliNaDveMista(mnozstvi);
            $(this.predpona+this.mnozstvi+this.zacatek+this.pripona).set('value',mnozstvi+"x");
            cena = porovnani.zaokrouhliNaDveMista(cena);
            $(this.predpona+this.cena+this.zacatek+this.pripona).set('value',cena+",-");
        }
        catch(e){
            pokladna.zpracujHlaseni("Technick? chyba, kontaktujte pros?m technickou podporu.");
        }
    },

    pridejRadek: function(nazev, mnozstvi, cena){
        if(this.aktualniPosledniPozice == this.konec){
            this.posunRadky(false);
        }
        else{
            this.posunRadky(true);
            this.aktualniPosledniPozice++;
        }
        this.vytvorRadek(nazev, mnozstvi, cena);
    },

    jePrazdna: function(){
        if($(this.predpona+this.nazev+this.zacatek+this.pripona).get('value') == "" && $(this.predpona+this.mnozstvi+this.zacatek+this.pripona).get('value') == "" && $(this.predpona+this.cena+this.zacatek+this.pripona).get('value') == ""){
            return true;
        }
        return false;
    }
});

var Porovnavani = new Class({
    jeCislo: function(cislo){
        if(cislo.search(/[0-9]/) != -1){
            return true;
        }
        return false;
    },

    jeKalkZnak: function(cislo){
        if(cislo.search(/[0-9\.]/) != -1){
            return true;
        }
        return false;
    },

    jeZnakCislo: function(znak){
        if(znak.length != 1){
            return false;
        }
        if(znak.search(/[0-9a-zA-Z]/) != -1){
            return true;
        }
        return false;
    },

    zaokrouhliNaDveMista: function(cislo1){
        pomocna = cislo1 * 100;
        cislo1 = Math.round(pomocna) / 100;
        pomocna3 = cislo1 + "";
        pomocna2 = pomocna3.split('.');
        if(pomocna2.length == 2){
            if(pomocna2[1].length < 2){
                while(pomocna2[1].length < 2){
                    pomocna2[1] = pomocna2[1] + "0";
                }
                cislo1 = pomocna2[0]+"."+pomocna2[1];
            }
            else{
                cislo1 = pomocna2[0]+"."+pomocna2[1];
            }
        }
        else{
            cislo1 = pomocna2[0]+".00";
        }
        return cislo1;
    }
});

var TvorbaElementu = new Class({
    POZICE_POZADI: "url('images/sedive.png')",
    PODKLAD: "podklad",

    vytvorDiv: function(styly, atributy, kam){
        return this.vytvorElement(styly,atributy,kam,"div");
    },

    vytvorMeziVrstvu: function(){
        var prepravkaStyl = new Prepravka();
        var prepravkaAtribut = new Prepravka();
        prepravkaStyl.vloz("position","absolute");
        prepravkaStyl.vloz("top","0px");
        prepravkaStyl.vloz("left","0px");
        prepravkaStyl.vloz("width","100%");
        prepravkaStyl.vloz("height","2000px");
        prepravkaStyl.vloz("display","");
        prepravkaStyl.vloz("background-image",this.POZICE_POZADI);
        prepravkaStyl.vloz("background-color","transparent");
        prepravkaStyl.vloz("z-index","8");
        prepravkaAtribut.vloz("id",this.PODKLAD);
        this.vytvorDiv(prepravkaStyl, prepravkaAtribut, document.getElementsByTagName("body")[0]);

    },

    vytvorObecnyDiv: function(){
        var prepravkaStyl = new Prepravka();
        var prepravkaAtribut = new Prepravka();
        prepravkaStyl.vloz("position","absolute");
        prepravkaStyl.vloz("top","0px");
        prepravkaStyl.vloz("left","0px");
        prepravkaStyl.vloz("display","");
        prepravkaStyl.vloz("background-color","white");
        prepravkaStyl.vloz("z-index","9");
        this.vytvorDiv(prepravkaStyl, prepravkaAtribut, document.getElementsByTagName("body")[0]);
    },

    vytvorElement: function(styly, atributy, kam, ktery){
        elementN = document.createElement(ktery);
        pomocny = kam.appendChild(elementN);
        pomocny = $(pomocny);
        while(atributy.dalsi()){
            pomocny.set(atributy.vratAktKlic(),atributy.vratAktHodnotu());
        }
        while(styly.dalsi()){
            pomocny.setStyle(styly.vratAktKlic(), styly.vratAktHodnotu());
        }
        return pomocny;
    },

    pridejRadek: function(obsahBunek, tabulka){
        umisteni = tabulka.rows.length;
        radek = tabulka.insertRow(umisteni);
        for(i = 0; i < obsahBunek.length; i++){
            this.pridejBunku(radek, obsahBunek[i], i);
        }
        return radek;
    },

    pridejBunku: function(radek, obsahBunky, kam){
        bunka = radek.insertCell(i);
        bunka.set('html',obsahBunky);
    }
});

var Hlaseni = new Class({
    Implements: Events,

    PODKLAD: "podklad",
    HLASENI: "hlaseni",
    obsah: "",

    initialize: function(obsah){
        this.elementTv = new TvorbaElementu();
        this.obsah = obsah;
        this.hlasChybu();
    },

    uklidHlaseni: function(){
        $(this.PODKLAD).parentNode.removeChild($(this.PODKLAD));
        $(this.HLASENI).parentNode.removeChild($(this.HLASENI));
    },

    hlasChybu: function(){
        var prepravkaStyl = new Prepravka();
        var prepravkaAtribut = new Prepravka();
        prepravkaStyl.vloz("position","absolute");
        prepravkaStyl.vloz("top","100px");
        prepravkaStyl.vloz("left","200px");
        prepravkaStyl.vloz("width","400px");
        prepravkaStyl.vloz("height","200px");
        prepravkaStyl.vloz("display","");
        prepravkaStyl.vloz("background-color","white");
        prepravkaStyl.vloz("z-index","10");
        prepravkaStyl.vloz("border","solid 4px black");
        prepravkaAtribut.vloz("id",this.HLASENI);
        prepravkaAtribut.vloz("html", "<table><tr><td style=\"font-size: 20px;\">"+this.obsah+"</td></tr><tr><td>&nbsp;</td></tr><tr><td style=\"text-align: center;\"><input id=\"btHlaseni\"  style=\"font-size: 30px;\" type='button' value='OK'></td></tr></table>");
        this.elementTv.vytvorMeziVrstvu();
        this.elementTv.vytvorDiv(prepravkaStyl, prepravkaAtribut, document.getElementsByTagName("body")[0]);
        $('btHlaseni').addEvent("click",function(){
            this.uklidHlaseni();
            this.fireEvent("closehlaseni");
        }.bind(this));
    }
});

var Dotaz = new Class({
    Implements: Events,

    PODKLAD: "podklad",
    HLASENI: "hlaseni",
    obsah: "",

    initialize: function(obsah){
        this.elementTv = new TvorbaElementu();
        this.obsah = obsah;
        this.hlasChybu();
    },

    uklidHlaseni: function(){
        $(this.PODKLAD).parentNode.removeChild($(this.PODKLAD));
        $(this.HLASENI).parentNode.removeChild($(this.HLASENI));
    },

    hlasChybu: function(){
        var prepravkaStyl = new Prepravka();
        var prepravkaAtribut = new Prepravka();
        prepravkaStyl.vloz("position","absolute");
        prepravkaStyl.vloz("top","100px");
        prepravkaStyl.vloz("left","200px");
        prepravkaStyl.vloz("width","400px");
        prepravkaStyl.vloz("height","200px");
        prepravkaStyl.vloz("display","");
        prepravkaStyl.vloz("background-color","white");
        prepravkaStyl.vloz("z-index","10");
        prepravkaStyl.vloz("border","solid 4px black");
        prepravkaAtribut.vloz("id",this.HLASENI);
        prepravkaAtribut.vloz("html", "<table><tr><td colspan='2'  style=\"font-size: 20px;\">"+this.obsah+"</td></tr><tr><td colspan='2'>&nbsp;</td></tr><tr><td style=\"text-align: center;\"><input id=\"btAno\"  style=\"font-size: 30px;\"  type='button' value='Ano'></td><td style=\"text-align: center;\"><input id=\"btNe\"  style=\"font-size: 30px;\" type='button' value='Ne'></td></tr></table>");
        this.elementTv.vytvorMeziVrstvu();
        this.elementTv.vytvorDiv(prepravkaStyl, prepravkaAtribut, document.getElementsByTagName("body")[0]);
        $('btAno').addEvent("click",function(){
            this.uklidHlaseni();
            this.fireEvent("dotazano");
        }.bind(this));
        $('btNe').addEvent("click",function(){
            this.uklidHlaseni();
            this.fireEvent("dotazne");
        }.bind(this));
    }
});


