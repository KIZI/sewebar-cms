//window.addEvent('domready', function(){ alert('ready'); });

function KbiPostAjax(url)
{
	//var url = "http://sewebar.local/index.php?option=com_jucene&task=arsearch&format=raw";

	/*var query =  (<r><![CDATA[
		using o for i"http://psi.ontopedia.net/"
		o:composed_by($OPERA  : o:Work, o:Puccini : o:Composer)?
	]]></r>).toString();*/

	var myAjax = new Request(
		{
			url: url,
			method: 'post',
			// update: 
			onRequest: function(){
				$('messages').empty();
			},
			onSuccess: function(responseText){
				$('results').set('text', responseText);
			},
			onFailure: function(){
				$('results').set('text', 'Sorry, your request failed :(');
			},
			data:
			{
				source: $('source').value,
				query: $('query').value,
				xslt: $('xslt').value,
                parameters: $('params').value
			}
		}).send();	

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

function KbiUploadDocument(url)
{
    var myAjax = new Ajax(url,
        {
            method: 'post',
            update: $('results'),
            data:
            {
                source: $('source').getValue(),
                content: $('document').getValue()
            }
        }).request();

    $('messages').empty();

    return false;
}

function KbiDataDictionary(url)
{
    var myAjax = new Ajax(url,
        {
            method: 'get',
            update: $('results'),
            data:
            {
                source: $('source').getValue()
            }
        }).request();

    $('messages').empty();

    return false;
}

