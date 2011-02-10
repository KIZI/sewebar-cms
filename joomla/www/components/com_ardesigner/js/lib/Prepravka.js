/**
 * Třída sloužící pro přepravování spojených hodnot. Konkrétně jde o variantu
 * klíč-hodnota a práci s nimi.
 */
var Prepravka = new Class({
    pozice: -1,
    delka: 0,

    /**
     * Vytvoří prázdnou přepravku
     */
    initialize: function(){
        this.klic = new Array();
        this.hodnota = new Array();
    },

    /**
     * Vloží do přepravky klíč a hodnotu
     */
    vloz: function(klic, hodnota){
        this.klic.push(klic);
        this.hodnota.push(hodnota);
        this.delka++;
    },

    /**
     * Přesune aktuální index na další prvek, vrátí false pokud je další prvek
     * mimo pole
     */
    dalsi: function(){
        if(this.pozice+1 >= this.delka){
            return false;
        }
        this.pozice++;
        return true;
    },

    /**
     * Zjisti zda existuje dalsi prvek
     */
    jeDalsi: function(){
        if(this.pozice+1 >= this.delka){
            return false;
        }
        return true;
    },

    /**
     * Vrati klic-hodnota na pozici pozice.
     */
    vyberAktualni: function(){
        return new Array(this.klic[this.pozice],this.hodnota[this.pozice]);
    },

    /**
     * Vrátí na aktuální pozici na začátek pole.
     */
    naZacatek: function(){
        this.pozice = -1;
    },

    /**
     * Vrátí klíč na aktuální pozici
     */
    vratAktKlic: function(){
        return this.klic[this.pozice];
    },

    /**
     * Vrátí hodnotu na aktuální pozici
     */
    vratAktHodnotu: function(){
        return this.hodnota[this.pozice];
    },

    /**
     * Vrátí klíč na zadané pozici
     */
    vratKlic: function(misto){
        return this.klic[misto];
    },

    /**
     * Vrátí hodnotu na zadané pozici
     */
    vratHodnotu: function(misto){
        return this.hodnota[misto];
    },

    vratHodnotuKlic: function(klic){
        this.naZacatek();
        while(this.dalsi()){
            if(this.vratAktKlic() == klic){
                hodnota = this.vratAktHodnotu();
                this.naZacatek();
                return hodnota;
            }
        }
        this.naZacatek();
        return null;
    },

    /**
     * Vrátí v dvouprvkovém poli klíč a hodnotu na určité pozici.
     */
    vyberPozice: function(misto){
        return new Array(this.klic[misto],this.hodnota[misto]);
    },

    /**
     * Smaže prvek na dané pozici.
     */
    smaz: function(misto){
        this.klic.splice(misto, 1);
        this.hodnota.splice(misto, 1);
    },

    /**
     * Vrátí počet prvků, který obsahuje přepravka
     */
    vratDelku: function(){
        return this.delka;
    }
});

