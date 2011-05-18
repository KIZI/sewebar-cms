using System;
using System.Collections.Generic;
using System.IO;
using System.Web;
using System.Web.SessionState;
using System.Xml;
using LMWrapper.LISpMiner;

namespace SewebarWeb
{
	/// <summary>
	/// Summary description for Import1
	/// </summary>
	public class Import1 : IHttpHandler, IRequiresSessionState 
	{

		public void ProcessRequest(HttpContext context)
		{
			context.Response.ContentType = "text/xml";
			var document = new XmlDocument();
			var response = document.CreateElement("response");

			if (context.Session["LM"] != null && context.Session["LM"] is LISpMiner)
			{
				var lm = ((LISpMiner) context.Session["LM"]);
				var input = String.Format(@"{0}\xml\DataDictionary{1:yyyyMMdd-Hmmss}.pmml", AppDomain.CurrentDomain.GetData("DataDirectory"), DateTime.Now);

				var file = File.CreateText(input);
				file.Write(context.Request["content"]);
				file.Close();

				var importer = lm.Importer;
				importer.Input = input;
				importer.Import();

				response.InnerText = String.Format("Imported {0} to {1}", importer.Input, importer.Dsn);
			}
			
			response.SetAttribute("id", context.Session.SessionID);
			document.AppendChild(response);

			document.Save(context.Response.OutputStream);
		}

		public bool IsReusable
		{
			get
			{
				return false;
			}
		}
	}
}