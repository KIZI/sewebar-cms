﻿using System;
using System.IO;
using System.Runtime.Serialization;
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

		protected virtual XDocument XDocument
		{
			get
			{
				return new XDocument(
					new XDeclaration("1.0", "utf-8", "yes"),
					new XElement("response",
					             new XAttribute("status", this.Status.ToString().ToLower()),
					             new XElement("message", this.Message)
						)
					);
			}
		}

		public virtual string Write()
		{
			using (var sw = new StringWriter())
			{
				this.XDocument.Save(sw);

				return sw.ToString();
			}
		}
	}
}