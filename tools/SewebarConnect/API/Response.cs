using System.Net;
using System.Web;
using System.Xml.Linq;

namespace SewebarWeb.API
{
	public abstract class Response
	{
		public HttpContext HttpContext { get; private set; }

		public string Message { get; set; }

		public Status Status { get; set; }

		protected Response(HttpContext context)
		{
			this.HttpContext = context;
		}

		public abstract void Write();

		public virtual void WriteException()
		{
			this.HttpContext.Response.StatusCode = (int) HttpStatusCode.InternalServerError;
			this.HttpContext.Response.TrySkipIisCustomErrors = true;

			new XDocument(
				new XDeclaration("1.0", "utf-8", "yes"),
				new XElement("response",
				             new XAttribute("status", this.Status.ToString()),
				             new XElement("message", this.Message)
					)
				).Save(this.HttpContext.Response.OutputStream);
		}
	}
}