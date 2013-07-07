using System;
using System.Net;
using System.Net.Http;
using System.Web;
using System.Web.Http.Filters;
using SewebarConnect.API.Responses;
using log4net;

namespace SewebarConnect.API
{
	public class APIErrorHandlerAttribute : ExceptionFilterAttribute
	{
		private static readonly ILog Log = LogManager.GetLogger(typeof(APIErrorHandlerAttribute));

		private HttpStatusCode _statusCode = HttpStatusCode.InternalServerError;

		public HttpStatusCode StatusCode
		{
			get
			{
				return this._statusCode;
			}

			set
			{
				this._statusCode = value;
			}
		}

		public override void OnException(HttpActionExecutedContext filterContext)
		{
			if (filterContext == null)
			{
				throw new ArgumentNullException("filterContext");
			}

			Exception exception = filterContext.Exception;
			ExceptionResponse exceptionResponse = new ExceptionResponse(exception);

			Log.Error(exception);

			// If this is not an HTTP 500 (for example, if somebody throws an HTTP 404 from an action method), 
			// ignore it.
			if (new HttpException(null, exception).GetHttpCode() != (int)HttpStatusCode.InternalServerError)
			{
				// return;
			}

			// TODO: test if works
			// var response = filterContext.Request.CreateResponse(this.StatusCode, exceptionResponse);
			var response = new HttpResponseMessage
				{
					StatusCode = this.StatusCode,
					Content = new StringContent(exceptionResponse.Write())
					// response.ContentType = "application/xml"
				};

			filterContext.Response = response;
		}
	}
}