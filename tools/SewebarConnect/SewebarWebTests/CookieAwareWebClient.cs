using System.Net;

namespace SewebarWebTests
{
	internal class CookieAwareWebClient : WebClient
	{
		private readonly CookieContainer _cookieContainer = new CookieContainer();
		private string _lastPage;

		protected override WebRequest GetWebRequest(System.Uri address)
		{
			var request = base.GetWebRequest(address);

			if (request is HttpWebRequest)
			{
				var webRequest = (HttpWebRequest) request;

				webRequest.CookieContainer = _cookieContainer;
				
				if (_lastPage != null)
				{
					webRequest.Referer = _lastPage;
				}
			}

			_lastPage = address.ToString();
			
			return request;
		}
	}
}