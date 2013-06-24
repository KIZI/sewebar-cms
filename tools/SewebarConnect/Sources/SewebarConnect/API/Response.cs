using System.IO;
using System.Runtime.Serialization;
using System.Text;
using System.Xml;
using System.Xml.Linq;

namespace SewebarConnect.API
{
	[DataContract]
	public class Response
	{
		[DataMember(Name = "message")]
		public string Message { get; set; }

		[DataMember(Name = "Status")]
		public Status Status { get; set; }

		public Response()
		{
			this.Status = Status.Success;
		}

		public Response(string message)
			: this()
		{
			this.Message = message;
		}

		protected virtual XDocument XDocument
		{
			get
			{
				return new XDocument(
					new XDeclaration("1.0", "utf-8", "yes"),
					new XElement("response",
					             new XAttribute("status", this.Status.ToString().ToLower()),
					             GetBody()
						)
					);
			}
		}

		protected virtual XElement GetBody()
		{
			return new XElement("message", this.Message);
		}

		public virtual string Write()
		{
			using (var m = new MemoryStream())
			{
				using (var sw = new XmlTextWriter(m, Encoding.UTF8))
				{
					this.XDocument.Save(sw);
				}

				return Encoding.UTF8.GetString(m.ToArray());
			}
		}
	}
}