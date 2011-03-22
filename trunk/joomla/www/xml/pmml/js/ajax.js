//from: components/com_kbi/assets/js.js

function KbiPostArb(id, src_type, query_type)
{
	var service_url = '/index.php?option=com_kbi&amp;task=query&amp;format=raw';
	var params = document.getElementById('arb' + id).innerHTML;
	// element, do ktereho prijde vysledek
	var result = $('arb_result' + id);

	result.empty().addClass('ajax-loading');
	result.removeClass('hidden');
	result.addEvent('click', function(){result.removeClass('ajax-loading');});

	var myAjax = new Ajax(service_url,
		{
			method : 'post',
			update : result,
			data : {
				source : 3, // typ_zdroje (Lucene, Ontopia..)
				query : 2, // typ_dotazu (vyjimka, podobnost)
				parameters : params, // arBuilder = vygenerovane XML
				xslt : 2, // nic
			},
			onComplete : function(response) {
				result.removeClass('ajax-loading');
			},
			onFailure : function(error) {
				result.removeClass('ajax-loading');
				result.addClass('ajax-error');
				result.setAttribute('title', error.responseText);
			}
		}
	).request();

	return false;
}
