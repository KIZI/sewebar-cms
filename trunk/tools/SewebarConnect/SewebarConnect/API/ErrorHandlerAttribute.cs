using System;
using System.IO;
using System.Net;
using System.Web;
using System.Web.Mvc;
using System.Xml;
using SewebarConnect.API.Responses;
using SewebarConnect.Controllers;
using log4net;

namespace SewebarConnect.API
{
	public class ErrorHandlerAttribute : FilterAttribute, IExceptionFilter
	{
		private static readonly ILog Log = LogManager.GetLogger(typeof(ErrorHandlerAttribute));

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

		public void OnException(ExceptionContext filterContext)
		{
			if (filterContext == null)
			{
				throw new ArgumentNullException("filterContext");
			}

			if (filterContext.IsChildAction)
			{
				return;
			}

			// If custom errors are disabled, we need to let the normal ASP.NET exception handler
			// execute so that the user can see useful debugging information. 
			if (filterContext.ExceptionHandled || !filterContext.HttpContext.IsCustomErrorEnabled)
			{
				// return;
			}

			Exception exception = filterContext.Exception;

			Log.Error(exception);

			// If this is not an HTTP 500 (for example, if somebody throws an HTTP 404 from an action method), 
			// ignore it.
			if (new HttpException(null, exception).GetHttpCode() != 500)
			{
				// return;
			}

			filterContext.ExceptionHandled = true;
			filterContext.HttpContext.Response.Clear();
			filterContext.HttpContext.Response.StatusCode = (int)this.StatusCode;
			filterContext.HttpContext.Response.ContentType = "application/xml";

			using (var stream = new StreamWriter(filterContext.HttpContext.Response.OutputStream))
			{
				var response = new ExceptionResponse(exception.Message);

				stream.Write(response.Write());
			}

			// Certain versions of IIS will sometimes use their own error page when 
			// they detect a server error. Setting this property indicates that we
			// want it to try to render ASP.NET MVC's error page instead.
			filterContext.HttpContext.Response.TrySkipIisCustomErrors = true;
		}
	}
}
