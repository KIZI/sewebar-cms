"use strict";

var config = null;

var reload = function() {
    $(config.getRootElementID()).fireEvent('reload');
};

var close = function() {
    $(config.getRootElementID()).fireEvent('closeOverlay');
};

var reloadReports = function() {
    $(config.getRootElementID()).fireEvent('reloadReports');
};
var reloadBRBase = function(){
    $(config.getRootElementID()).fireEvent('reloadBRBase');
}

window.addEvent('domready', function () {
    var nativeTypeExtender = new NativeTypeExtender();
    nativeTypeExtender.extendAll();

    config = new Config();

	var uri = new URI(window.location.href);
	config.setParams(uri.get('data'));

	var ARB = new ARBuilder(config);
});

