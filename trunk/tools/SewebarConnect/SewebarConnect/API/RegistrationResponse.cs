using System.IO;
using System.Runtime.Serialization;
using System.Xml.Linq;
using LMWrapper;

namespace SewebarConnect.API
{
	[DataContract(Name = "response")]
	public class RegistrationResponse : Response
	{
		[DataMember(Name = "id")]
		public ShortGuid Id { get; set; }

		protected override XDocument XDocument
		{
			get
			{
				return new XDocument(
					new XDeclaration("1.0", "utf-8", "yes"),
					new XElement("response",
					             new XAttribute("status", Status.Success.ToString().ToLower()),
					             new XAttribute("id", this.Id.Value)
						)
					);
			}
		}
	}
}