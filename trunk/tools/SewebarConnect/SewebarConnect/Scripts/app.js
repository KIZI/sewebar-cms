$(document).ready(function () {
	// ajax links
	$('.ajax').click(function () {
		var me = $(this);
		$.ajax({
			url: $(this).attr('href')
		}).done(function () {
			me.remove();
		}).fail(function (data) {
			alert($('message', data.responseXML).text());
		}); ;

		return false;
	});
});