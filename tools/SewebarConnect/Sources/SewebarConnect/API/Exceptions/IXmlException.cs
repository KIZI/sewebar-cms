using System.Xml.Linq;

namespace SewebarConnect.API.Exceptions
{
	interface IXmlException
	{
		XDocument ToXDocument();
	}
}
