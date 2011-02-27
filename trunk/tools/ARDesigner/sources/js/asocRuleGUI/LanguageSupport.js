/**
 * Class: LanguageSupport
 * It adds support for more languages to the aplications.
 */
var LanguageSupport = new Class({
    CONNECTIVES: "Connectives",
    INTEREST_MEASURES: "InterestMeasures",
    FIELDS: "Fields",
    NEW_RULE: "NewRule",
    SAVE: "Save",
    NEED_MORE_ELEMENTS: "NeedMoreElements",
    EVERYTHING_OK: "EverythingOK",
    INCORRECT_RULE: "IncorrectRule",

    /**
     * Function: initialize
     * It creates instance of LanguageSupport
     */
    initialize: function(){
        this.langDatas = new Array();
        this.initLang();
    },

    /**
     * Function: initLang
     * It creates fields asociated to each language, which is then possible to get
     * out of this class.
     */
    initLang: function(){
        var specificLangCs = new Array();
        specificLangCs[this.CONNECTIVES] = "Spojky";
        specificLangCs[this.INTEREST_MEASURES] = "M�ry zaj�mavosti(Kvantifik�tory)";
        specificLangCs[this.FIELDS] = "Pole(Attributy)";
        specificLangCs[this.NEW_RULE] = "Nov� pravidlo1";
        specificLangCs[this.SAVE] = "Ulo�";
        specificLangCs[this.NEED_MORE_ELEMENTS] = "Je pot�eba v�t�� mno�stv� prvk� bu� celkov� nebo v antecedentu nebo v consequentu nebo v�ce m�r zaj�mavosti.";
        specificLangCs[this.EVERYTHING_OK] = "V�e bylo v po��dku ulo�eno.";
        specificLangCs[this.INCORRECT_RULE] = "N�kter� z pravidel nen� v po��dku.";
        this.langDatas["cs"] = specificLangCs;

        var specificLangEn = new Array();
        specificLangEn[this.CONNECTIVES] = "Connectives";
        specificLangEn[this.INTEREST_MEASURES] = "Interest measures(Quantifiers)";
        specificLangEn[this.FIELDS] = "Fields(Attributes)";
        specificLangEn[this.NEW_RULE] = "New rule";
        specificLangEn[this.SAVE] = "Save";
        specificLangEn[this.NEED_MORE_ELEMENTS] = "It is necessary to add more elements of the rule either in antecedent or in consequent or there must be more interest measures. ";
        specificLangEn[this.EVERYTHING_OK] = "Everything was saved succesfully.";
        specificLangEn[this.INCORRECT_RULE] = "Some of the rules was not correct.";
        this.langDatas["en"] = specificLangEn;
    },

    /**
     * Function: getName
     * It returns appropriate Array of names depending on language.
     *
     * Parameters:
     * name     {String} Name of explanation.
     * lang     {String} Language as abbreviation like 'cs' or 'en'
     *
     * Returns:
     * {String} name in appropriate language
     */
    getName: function(name, lang){
        return this.langDatas[lang][name];
    }
});

