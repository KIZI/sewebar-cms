using System;
using System.IO;
using System.Runtime.Serialization;
using System.Text;
using System.Web;
using System.Web.Mvc;
using System.Xml;
using System.Xml.Linq;

namespace SewebarConnect.API
{
	public class XmlResult : System.Web.Mvc.ActionResult
	{
		public Encoding ContentEncoding { get; set; }

		public string ContentType { get; set; }

		public Response Data { get; set; }

		public override void ExecuteResult(ControllerContext context)
		{
			if (context == null)
			{
				throw new ArgumentNullException("context");
			}

			HttpResponseBase response = context.HttpContext.Response;
			XDocument xdoc = null;

			if (!String.IsNullOrEmpty(ContentType))
			{
				response.ContentType = ContentType;
			}
			else
			{
				response.ContentType = "text/xml";
			}
			if (ContentEncoding != null)
			{
				response.ContentEncoding = ContentEncoding;
			}

			if (this.Data != null)
			{
				using (var w = new StreamWriter(response.OutputStream))
				{
					w.Write(this.Data.Write());
				}
			}
		}
	}
}
