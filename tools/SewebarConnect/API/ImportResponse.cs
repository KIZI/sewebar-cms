using System.Web;
using System.Xml.Linq;

namespace SewebarWeb.API
{
	public class ImportResponse : Response
	{
		public string Id { get; set; }

		public ImportResponse(HttpContext context) : base(context)
		{
		}

		public override void Write()
		{
			this.HttpContext.Response.ContentType = "text/xml";

			new XDocument(
				new XDeclaration("1.0", "utf-8", "yes"),
				new XElement("response",
					new XAttribute("id", this.Id),
					new XAttribute("status", this.Status.ToString())
				)
			).Save(this.HttpContext.Response.OutputStream);
		}
	}
}

