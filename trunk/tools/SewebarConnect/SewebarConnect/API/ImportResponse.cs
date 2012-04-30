using System.Xml.Linq;

namespace SewebarConnect.API
{
	public class ImportResponse : Response
	{
		public string Id { get; set; }

		protected override XDocument XDocument
		{
			get
			{
				return new XDocument(
					new XDeclaration("1.0", "utf-8", "yes"),
					new XElement("response",
					             new XAttribute("id", this.Id),
					             new XAttribute("status", this.Status.ToString().ToLower()),
								 new XElement("message", this.Message)
						)
					);
			}
		}
	}
}