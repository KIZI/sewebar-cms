$(document).ready(function() {
	$.ajax({
		url : "/index.php?option=com_kbi&task=query&format=raw",
		type : "POST",
		data : {
			source: {
				"url": "http:\/\/nlp.vse.cz:8081\/xquery_search\/xquery_servlet",
				"type": "XQUERY"
			},
			query: 2,
			xslt: 2
		},
		success : function(msg, status) {
			$('body').html(msg);
		}
	});
});