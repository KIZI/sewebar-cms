using System;
using System.Collections.Generic;
using System.IO;
using System.Web;
using System.Web.SessionState;
using System.Xml;
using System.Xml.XPath;
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

			if (context.Session["LM"] != null && context.Session["LM"] is LISpMiner && context.Request["content"] != null)
			{
				var taskName = "T2";//String.Format("Task{0:yyyyMMdd-Hmmss}", DateTime.Now);

				using (var stream = new StringReader(context.Request["content"]))
				{
					var xpath = new XPathDocument(stream);
					var docNav = xpath.CreateNavigator();

					var nsmgr = new XmlNamespaceManager(docNav.NameTable);
					nsmgr.AddNamespace("guha", "http://keg.vse.cz/ns/GUHA0.1rev1");
					nsmgr.AddNamespace("pmml", "http://www.dmg.org/PMML-4_0");

					taskName = docNav.SelectSingleNode("/pmml:PMML/guha:AssociationModel/@modelName", nsmgr).Value;
				}

				var taskXmlPath = String.Format("{0}/xml/{1:yyyyMMdd-Hmmss}_task.pmml",
				                                AppDomain.CurrentDomain.GetData("DataDirectory"), DateTime.Now);

				using (var file = new StreamWriter(taskXmlPath))
				{
					file.Write(context.Request["content"]);
				}

				var importer = ((LISpMiner)context.Session["LM"]).Importer;
				importer.Input = taskXmlPath;
				importer.Import();
				//response.InnerText += importer.Arguments;
				//response.InnerText += String.Format("<br/>Imported task {0} to {1}", importer.Input, importer.Dsn);
				
				var task4FtGen = ((LISpMiner)context.Session["LM"]).Task4FtGen;
				task4FtGen.TaskName = taskName;
				task4FtGen.Run();
				//response.InnerText += String.Format("Runnig the task: {0}", task4FtGen.Arguments);
				
				var exporter = ((LISpMiner)context.Session["LM"]).Exporter;
				exporter.Output = String.Format("{0}/xml/{1:yyyyMMdd-Hmmss}_results.xml", AppDomain.CurrentDomain.GetData("DataDirectory"), DateTime.Now);
				exporter.Template = String.Format(@"{0}\Sewebar\Template\ARDExport.LM.Template.txt", exporter.LMPath);
				exporter.Alias = String.Format(@"{0}\Sewebar\Template\LM.PMML.Alias.ARD.txt", exporter.LMPath);
				exporter.TaskName = taskName;
				exporter.Export();
				//response.InnerText += String.Format("Results exported to {0} ({1})", exporter.Output, exporter.Arguments);

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