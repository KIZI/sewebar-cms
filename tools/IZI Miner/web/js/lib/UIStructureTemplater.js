var UIStructureTemplater = new Class({

    register: function() {
        this.registerStructure();
        this.registerSettings();
        this.registerNavigation();
        this.registerActiveRule();
        this.registerMarkedRules();
    },

    registerStructure: function() {
        Mooml.register('overlayTemplate', function () {
            section({id: 'overlay'});
        });

        Mooml.register('headerTemplate', function (data) {
            var i18n = data.i18n,
                config = data.config;

            header(div({id: 'settings'},
                a({href: '#', id: 'new-task'}, i18n.translate('New task')),
                a({href: '#', id: 'settings-open'}, i18n.translate('Settings'))),
                h1(config.getName() + '<sup>' + config.getVersion() + '</sup><span>' + config.getSlogan() + '</span>'))
        });

        Mooml.register('mainTemplate', function (data) {
            var i18n = data.i18n;

            div({id: 'wrapper', 'class': 'clearfix'},
                section({id: 'workplace', 'class': 'clearfix'},
                    section({id: 'content'},
                        section({id: 'active-rule'}),
                        section({id: 'found-rules'},
                            h2(i18n.translate('Discovered rules')),
                            div({id: 'pager-label'}),
                            a({id: 'stop-mining', href: '#'}, i18n.translate('Stop mining')),
                            div({id: 'paging'}),
                            div({id: 'pager'},
                                ul({'class': 'scroller'})),
                            a({id: 'view-task-setting', href: '#', target: '_blank'}, i18n.translate('Task setting')),
                            a({id: 'view-task-result', href: '#', target: '_blank'}, i18n.translate('Task result')),
                            a({id: 'pager-clear', href: '#'}, i18n.translate('Clear rules'))
                        )
                    )
                ),
                nav({id: 'navigation'})
            );
        });

        Mooml.register('footerTemplate', function (data) {
            var i18n = data.i18n,
                config = data.config,
                dateHelper = data.dateHelper;

            footer('&copy; ' + dateHelper.getYear() + ' ' + config.getCopyright() + ', ' + i18n.translate('created by') + ' ' + config.getAuthor())
        });
    },

    registerSettings: function() {
        Mooml.register('newTaskTemplate', function (data) {
            var url = data.url;

            div({id: 'new-task-window'},
                iframe({src: url}));
        });

        Mooml.register('settingsTemplate', function (data) {
            var autoSuggestPossible = data.autoSuggestPossible,
                i18n = data.i18n,
                reset = data.reset,
                settings = data.settings;

            div({id: 'settings-window'},
                a({id: 'settings-close', href: '#'}, i18n.translate('Close')),
                h2(i18n.translate('Settings')),
                form({action: '#', method: 'POST', id: 'settings-form'},
                    div(
                        span({'class': 'category'}, i18n.translate('Association rule pattern restrictions'))),
                    div({'class': 'autocomplete'},
                        div(
                            label({'for': 'fl-select'}, i18n.translate('Restrictions') + ':'),
                            select({name: 'fl-select', id: 'fl-select'}),
                            reset ? span({'class': 'tooltip warning'},
                                span({'class': 'warning'},
                                    img({src: './images/icon-tooltip-warning.png'}),
                                    em(i18n.translate('Association rule pattern reset')),
                                    i18n.translate('Association rule pattern has to be reset due to new restrictions.'))) : '',
                            span({'class': 'tooltip info'},
                                span({'class': 'help'},
                                    img({src: './images/icon-tooltip-help.png'}),
                                    em(i18n.translate('Restrictions')),
                                    i18n.translate('These are predefined association rule pattern restrictions, which do not depend on analysed data. The more expert the looser they are.')))),
                        div(
                            label({'for': 'as-select', 'class': 'thin'}, i18n.translate('Attribute<br>suggestion') + ':'),
                            autoSuggestPossible ? a({id: 'as', 'href': '#', 'class': settings.getRecEnabled() ? 'autosuggest-on' : 'autosuggest-off'}, i18n.translate(settings.getRecEnabled() ? 'On': 'Off')) : span({'class': 'autosuggest-off'}, i18n.translate(settings.getRecEnabled() ? 'On': 'Off')),
                            span({id: 'as-select'}))),
                    div(
                        span({'class': 'category'}, i18n.translate('Discovered rules'))),
                    div(
                        label({'for': 'rulesCnt'}, i18n.translate('Limit') + ':'),
                        input({id: 'rules-cnt', 'type': 'text', 'class': 'shortnr', value: settings.getRulesCnt()}),
                        span({'class': 'tooltip info'},
                            span({'class': 'help'},
                                img({src: './images/icon-tooltip-help.png'}),
                                em(i18n.translate('Limit')),
                                i18n.translate('Maximal number of association rules to be searched for. If the limit is reached and there are more rules to find, an option to search for the remaining rules pops up.')))),
                    div(
                        label({'for': 'as-select'}, i18n.translate('Auto filter') + ':'),
                        a({id: 'autofilter', 'href': '#', 'class': settings.getBKAutoSearch() ? 'autofilter-on' : 'autofilter-off'}, i18n.translate(settings.getBKAutoSearch() ? 'On': 'Off')),
                        span({'class': 'tooltip info'},
                            span({'class': 'help'},
                                img({src: './images/icon-tooltip-help.png'}),
                                em(i18n.translate('Auto filter')),
                                i18n.translate('Association rules are automaticaly filtered according to expert background knowledge. This guarantees that only interesting association rules are left.')))),
                    br({'class': 'clearfix'}),
                    input({type: 'submit', value: i18n.translate('Save')})));
        });

        Mooml.register('flOptionTemplate', function (data) {
            var FL = data.FL,
                isSelected = data.isSelected;

            if (isSelected === true) {
                option({'value': FL.getName(), 'selected': 'selected'}, FL.getLocalizedName());
            } else {
                option({'value': FL.getName()}, FL.getLocalizedName());
            }
        });
    },

    registerNavigation: function() {
        Mooml.register('attributesStructureTemplate', function (data) {
            var byGroup = data.byGroup,
                inProgress = data.inProgress,
                i18n = data.i18n;

            if (byGroup) {
                section({id: 'attributes'},
                    h2({'class': 'minimize'}, i18n.translate('Attributes'), a({href: '#', 'class': 'toggle'}, '')),
                    div(
                        ul(),
                        span({id: 'etree-progress', styles: {'visibility': inProgress ? 'visible' : 'hidden'}}, i18n.translate('Sort in progress.')),
                        div(a({id: 'attributes-by-list', href: '#'}, i18n.translate('attributes')))));
            } else {
                section({id: 'attributes'},
                    h2({'class': 'minimize'}, i18n.translate('Attributes'), a({href: '#', 'class': 'toggle'}, '')),
                    div(
                        ul({'class': 'clearfix'}),
                        span({id: 'etree-progress', styles: {'visibility': inProgress ? 'visible' : 'hidden'}}, i18n.translate('Sort in progress.'))//,
//						div(a({id: 'attributes-by-group', href: '#'}, i18n.translate('predefined attributes')))
                    ));
            }
        });

        Mooml.register('dataFieldsStructureTemplate', function (data) {
            var i18n = data.i18n;

            section({id: 'data-fields'},
                h2({'class': 'minimize'}, i18n.translate('Data fields'), a({href: '#', 'class': 'toggle'}, '')),
                div(
                    ul({'class': 'clearfix'})));
        });
    },

    registerActiveRule: function() {
        Mooml.register('activeRuleTemplate', function (data) {
            var rules = data.rules,
                attributes = data.attributes,
                taskBox = rules || attributes,
                i18n = data.i18n,
                displayAddIM = data.displayAddIM;

            if (taskBox) {
                var taskText = i18n.translate('Do you want to');
                if (rules && attributes) {
                    taskText += ' <a href="#" id="mine-rules-confirm">' + i18n.translate('mine rules') + '</a> ' + i18n.translate('or') + ' <a href="#" id="recommend-attributes-confirm">' + i18n.translate('recommend next attribute') + '</a>' + '?';
                } else if (rules) {
                    taskText += ' <a href="#" id="mine-rules-confirm">' + i18n.translate('mine rules') + '</a>' + '?';
                } else if (attributes) {
                    taskText += ' <a href="#" id="recommend-attributes-confirm">' + i18n.translate('recommend next attribute') + '</a>' + '?';
                }
            }

            section({id: 'active-rule'},
                h2(i18n.translate('Association rule pattern')),
                    div({id: 'ar-wrapper', 'class': 'clearfix'},
                        div({id: 'antecedent'}, h3(i18n.translate('Antecedent'))),
                        div({id: 'interest-measures'},
                            h3(i18n.translate('Interest measures')),
                            div(),
                            displayAddIM ? a({href: '#', id: 'add-im'}, i18n.translate('Add IM')) : ''),
                        div({id: 'succedent'}, h3(i18n.translate('Consequent')))
                    ),
                div({'class': 'clearfix'}),
                span({id: 'action-box', styles: {'visibility': taskBox ? 'visible' : 'hidden'}}, taskText));
        });
    },

    registerMarkedRules: function() {
        Mooml.register('markedRulesStructureTemplate', function (data) {
            var i18n = data.i18n;

            section({id: 'marked-rules'},
                h2({'class': 'minimize'}, i18n.translate('Rule clipboard'), a({href: '#', 'class': 'toggle'}, '')),
                div(
                    ul()));
        });
    }

});