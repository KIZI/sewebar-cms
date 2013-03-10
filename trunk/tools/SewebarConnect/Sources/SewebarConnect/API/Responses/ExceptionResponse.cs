using System;
using System.Xml.Linq;
using SewebarConnect.API.Exceptions;

namespace SewebarConnect.API.Responses
{
	public class ExceptionResponse : Response
	{
		public Exception Exception { get; protected set; }

		public ExceptionResponse(Exception exception)
		{
			this.Exception = exception;
			this.Message = this.Exception.Message;
			this.Status = Status.Failure;
		}

		protected override XDocument XDocument
		{
			get
			{
				var xmlException = this.Exception as IXmlException;

				if (xmlException != null)
				{
					return xmlException.ToXDocument();
				}

				return new XDocument(
					new XDeclaration("1.0", "utf-8", "yes"),
					new XElement("response",
								 new XAttribute("status", this.Status.ToString().ToLower()),
								 new XElement("message", this.Message)
						)
					);
			}
		}
	}
}