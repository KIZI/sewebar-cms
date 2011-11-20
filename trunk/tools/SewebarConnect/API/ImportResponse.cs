using System;
using System.Xml.Linq;

namespace SewebarWeb.API
{
	public class ImportResponse
	{
		public string Id { get; set; }
		
		public string Message { get; set; }
		
		public Status Status { get; set; }
		
		public XDocument ToXml()
		{			
			return new XDocument(
				new XDeclaration("1.0", "utf-8", "yes"),
				new XElement("response", 
					new XAttribute("id", this.Id),
					new XAttribute("status", this.Status.ToString())
				)
			);
		}
	}
}

