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
    INCORRECT_FIELD_VALUE: "IncorrectField",
    MINING_SETTING_CREATE: "MiningSettingCreate",
    RULE_STATE_INCOMPLETE: "RuleStateIncomplete",
    RULE_STATE_COMPLETE: "RuleStateComplete",
    HITS_LABEL: "HitsLabel",
    HITS_LABEL_FOUND: "HitsLabelFound",
    HITS_LABEL_LOADING: "HitsLabelLoading",
    actualLang: "cs",

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
        specificLangCs[this.INCORRECT_FIELD_VALUE] = "Hodnota zadan� v poli nespl�uje parametry.";
        specificLangCs[this.MINING_SETTING_CREATE] = "Do n�sleduj�c�ho pole zadejte zad�n� pro minov�n�:";
        specificLangCs[this.RULE_STATE_INCOMPLETE] = "Pravidlo nen� kompletn�, pokra�ujte v up�esn�n� zad�n� pro minov�n�.";
        specificLangCs[this.RULE_STATE_COMPLETE] = "Pravidlo je kompletn�, m��ete ho d�le upravovat.";
        specificLangCs[this.HITS_LABEL] = "Nalezen� pravidla se zobraz� zde:";
        specificLangCs[this.HITS_LABEL_FOUND] = "Po�et nalezen�ch pravidel: ";
        specificLangCs[this.HITS_LABEL_LOADING] = '<img src="./sources/assets/loading.gif" /> (Prob�h� minov�n� pravidel)';
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
        specificLangEn[this.INCORRECT_FIELD_VALUE] = "Field value is incorrect.";
        specificLangEn[this.MINING_SETTING_CREATE] = "Drag & Drop the mining setting into the placeholder below:";
        specificLangEn[this.RULE_STATE_INCOMPLETE] = "The rule is not valid. Please continue to create valid task setting.";
        specificLangEn[this.RULE_STATE_COMPLETE] = "The rule is valid.";
        specificLangEn[this.HITS_LABEL] = "The results will be shown here:";
        specificLangEn[this.HITS_LABEL_FOUND] = "Number of results found: ";
        specificLangEn[this.HITS_LABEL_LOADING] = '<img src="./sources/assets/loading.gif" /> (Mining of the results is in progress)';
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

