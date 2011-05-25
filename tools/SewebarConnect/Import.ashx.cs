using System;
using System.IO;
using System.Web;
using System.Web.SessionState;
using System.Xml.Linq;
using LMWrapper.LISpMiner;

namespace SewebarWeb
{
	public class Import : IHttpHandler, IRequiresSessionState 
	{
		public void ProcessRequest(HttpContext context)
		{
			context.Response.ContentType = "text/xml";
			var response = new XElement("response");
			
			if (context.Session["LM"] != null && context.Session["LM"] is LISpMiner)
			{
				var lm = ((LISpMiner) context.Session["LM"]);
				var input = String.Format(@"{0}\xml\DataDictionary{1:yyyyMMdd-Hmmss}.pmml", AppDomain.CurrentDomain.GetData("DataDirectory"), DateTime.Now);

				var file = File.CreateText(input);
				file.Write(context.Request["content"]);
				file.Close();

				var importer = lm.Importer;
				importer.Input = input;
				importer.Launch();

				response.Value = String.Format("Imported {0} to {1}", importer.Input, importer.Dsn);
			}

			response.SetAttributeValue("id", context.Session.SessionID);

			new XDocument(
				new XDeclaration("1.0", "utf-8", "yes"),
				response
			).Save(context.Response.OutputStream);
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