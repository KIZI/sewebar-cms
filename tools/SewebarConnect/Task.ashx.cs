using System;
using System.Collections.Generic;
using System.Web;
using System.Web.SessionState;
using System.Xml;
using LMWrapper.LISpMiner;

namespace SewebarWeb
{
	/// <summary>
	/// Summary description for Task1
	/// </summary>
	public class Task1 : IHttpHandler, IRequiresSessionState
	{
		public void ProcessRequest(HttpContext context)
		{
			context.Response.ContentType = "text/xml";
			var document = new XmlDocument();
			var response = document.CreateElement("response");

			if (context.Session["LM"] != null && context.Session["LM"] is LISpMiner)
			{
				var importer = ((LISpMiner)context.Session["LM"]).Importer;
				importer.Input = String.Format(@"{0}\xml\barbora2_radek.pmml", AppDomain.CurrentDomain.GetData("DataDirectory"));
				importer.Import();
				response.InnerText += String.Format("Imported task {0} to {1}", importer.Input, importer.Dsn);

				var task4FtGen = ((LISpMiner)context.Session["LM"]).Task4FtGen;
				task4FtGen.TaskId = "15";
				task4FtGen.Run();
				response.InnerText += String.Format("Runnig the task: {0}", task4FtGen.Arguments);
				
				var exporter = ((LISpMiner)context.Session["LM"]).Exporter;
				exporter.Output = String.Format("{0}/results.xml", AppDomain.CurrentDomain.BaseDirectory);
				exporter.Template = String.Format(@"{0}\Sewebar\Template\4ftMiner.Task.PMML.ARBuilder.Template.txt", exporter.LMPath);
				exporter.Alias = String.Format(@"{0}\Sewebar\Template\LM.PMML.Alias.txt", exporter.LMPath);
				exporter.TaskName = "TaskM";
				exporter.Export();
				response.InnerText += String.Format("Results exported to {0} ({1})", exporter.Output, exporter.Arguments);

				context.Response.WriteFile(exporter.Output);
			}

			response.SetAttribute("id", context.Session.SessionID);
			document.AppendChild(response);

			//document.Save(context.Response.OutputStream);
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