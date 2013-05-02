using System.Net;
using System.Net.Http;
using System.Security;
using System.Web.Http.Filters;

namespace SewebarConnect.Security
{
	public class SecurityExceptionFilter : ExceptionFilterAttribute
	{
		public override void OnException(HttpActionExecutedContext actionExecutedContext)
		{
			if (actionExecutedContext.Exception is SecurityException)
			{
				var response = new HttpResponseMessage(HttpStatusCode.Unauthorized)
					{
						Content = new StringContent(actionExecutedContext.Exception.Message)
					};

				actionExecutedContext.Response = response;
			}
		}
	}
}