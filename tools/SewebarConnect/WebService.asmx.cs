using System;
using System.Collections.Generic;
using System.IO;
using System.Web;
using System.Web.Services;

namespace SewebarWeb
{
	/// <summary>
	/// Summary description for WebService
	/// </summary>
	[WebService(Namespace = "http://tempuri.org/")]
	[WebServiceBinding(ConformsTo = WsiProfiles.BasicProfile1_1)]
	[System.ComponentModel.ToolboxItem(false)]

	public class WebService : System.Web.Services.WebService
	{

		[WebMethod(EnableSession = true)]
		public string HelloWorld(string dataDescription)
		{
			return dataDescription;
		}
	}
}
