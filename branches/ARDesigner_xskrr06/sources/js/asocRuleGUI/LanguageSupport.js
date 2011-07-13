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
    HITS_LABEL_LOADING_IMG: "HitsLabelLoadingImg",
    HITS_LIMIT: "HitsLimit",
    HITS_LIMIT_REACHED: "HitsLimitReached",
    HITS_SEARCH_AGAIN: "HitsSearchAgain",
    HITS_SOURCE: "HitsSource",
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
        specificLangCs[this.INTEREST_MEASURES] = "Míry zajímavosti(Kvantifikátory)";
        specificLangCs[this.FIELDS] = "Pole(Attributy)";
        specificLangCs[this.NEW_RULE] = "Nové pravidlo1";
        specificLangCs[this.SAVE] = "Ulož";
        specificLangCs[this.NEED_MORE_ELEMENTS] = "Je potřeba větší množství prvků buď celkově nebo v antecedentu nebo v consequentu nebo více měr zajímavosti.";
        specificLangCs[this.EVERYTHING_OK] = "Vše bylo v pořádku uloženo.";
        specificLangCs[this.INCORRECT_RULE] = "Některé z pravidel není v pořádku.";
        specificLangCs[this.INCORRECT_FIELD_VALUE] = "Hodnota zadaná v poli nesplňuje parametry.";
        specificLangCs[this.MINING_SETTING_CREATE] = "Do následujícího pole zadejte zadání pro minování:";
        specificLangCs[this.RULE_STATE_INCOMPLETE] = "Pravidlo není kompletní, pokračujte v upřesnění zadání pro minování.";
        specificLangCs[this.RULE_STATE_COMPLETE] = "Pravidlo je kompletní, můžete ho dále upravovat.";
        specificLangCs[this.HITS_LABEL] = "Nalezená pravidla se zobrazí zde:";
        specificLangCs[this.HITS_LABEL_FOUND] = "Počet nalezených pravidel: ";
        specificLangCs[this.HITS_LABEL_LOADING] = '(Probíhá minování pravidel)';
        specificLangCs[this.HITS_LABEL_LOADING_IMG] = '<span class="loading">&nbsp;</span>';
        specificLangCs[this.HITS_LIMIT] = 'Maximální počet výsledků na zdroj:';
        specificLangCs[this.HITS_LIMIT_REACHED] = '(Dosažen maximální počet výsledků)';
        specificLangCs[this.HITS_SEARCH_AGAIN] = 'Znovu vyhledat';
        specificLangCs[this.HITS_SOURCE] = 'Zdroj';
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
        specificLangEn[this.HITS_LABEL_LOADING] = '(Mining of the results is in progress)';
        specificLangEn[this.HITS_LABEL_LOADING_IMG] = '<span class="loading">&nbsp;</span>';
        specificLangEn[this.HITS_LIMIT] = 'Maximum number of results per source:';
        specificLangEn[this.HITS_LIMIT_REACHED] = '(Limit has been reached)';
        specificLangEn[this.HITS_SEARCH_AGAIN] = 'Search again';
        specificLangEn[this.HITS_SOURCE] = 'Source';
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

