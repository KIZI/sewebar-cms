using System.IO;
using System.Xml.Linq;

namespace SewebarConnect.API.Exceptions
{
	public class InvalidTaskResultXml : System.Exception, IXmlException
	{
		public string XmlPath { get; protected set; }

		public string XPath { get; protected set; }

		public InvalidTaskResultXml(string message, string xmlPath)
			: base(message)
		{
			this.XmlPath = xmlPath;
		}

		public InvalidTaskResultXml(string message, string xmlPath, string xPath)
			: this(message, xmlPath)
		{
			this.XPath = xPath;
		}

		public XDocument ToXDocument()
		{
			using (var reader = new StreamReader(this.XmlPath))
			{
				return new XDocument(
					new XDeclaration("1.0", "utf-8", "yes"),
					new XElement("response",
					             new XAttribute("status", Status.Failure.ToString().ToLower()),
					             new XElement("message", this.Message),
					             new XElement("data",
					                          new XAttribute("path", this.XmlPath),
					                          new XAttribute("xpath", this.XPath),
					                          new XCData(reader.ReadToEnd()))
						)
					);
			}
		}
	}
}
