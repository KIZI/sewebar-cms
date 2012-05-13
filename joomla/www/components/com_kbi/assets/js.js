//window.addEvent('domready', function(){ alert('ready'); });

function KbiPostAjax(url)
{
	//var url = "http://sewebar.local/index.php?option=com_jucene&task=arsearch&format=raw";

	/*var query =  (<r><![CDATA[
		using o for i"http://psi.ontopedia.net/"
		o:composed_by($OPERA  : o:Work, o:Puccini : o:Composer)?
	]]></r>).toString();*/

	var myAjax = new Ajax(url,
		{
			method: 'post',
			update: $('results'),
			data:
			{
				source: $('source').getValue(),
				query: $('query').getValue(),
				xslt: $('xslt').getValue(),
                parameters: $('params').getValue()
			}
		}).request();

	$('messages').empty();

	return false;
}

function KbiGetAjax(url)
{
	//var url = "http://sewebar.local/index.php?option=com_kbi&controller=server&format=raw";
	url += "&source=" + $('source').getValue();
	url += "&query=" + $('query').getValue();
	url += "&xslt=" + $('xslt').getValue();
    url += "&parameters=" + $('parameters').getValue();

	url = encodeURI(url);

	var myAjax = new Ajax(url, {method: 'get', update: $('results')}).request();

	$('messages').setHTML('<a href="' + myAjax.url + '" target="_blank">show</a>');

	return false;
}

