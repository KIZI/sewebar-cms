using System.Web;
using System.Xml.Linq;

namespace SewebarWeb.API
{
	public class RegisterResponse : Response
	{
		public string Id { get; set; }

		public RegisterResponse(HttpContext context)
			: base(context)
		{
		}

		public override void Write()
		{
			//this.HttpContext.Response.ContentType = "application/json";
			//this.HttpContext.Response.ContentType = "text/plain";
			this.HttpContext.Response.ContentType = "text/xml";

			if (this.Status == Status.failure)
			{
				this.WriteException();
				return;
			}

			new XDocument(
				new XDeclaration("1.0", "utf-8", "yes"),
				new XElement("response",
				             new XAttribute("status", this.Status.ToString()),
				             new XAttribute("id", this.Id)
					)
				).Save(this.HttpContext.Response.OutputStream);
		}
	}
}