using System;
using System.IO;
using System.Web;
using System.Web.SessionState;
using System.Xml;
using System.Xml.XPath;
using LMWrapper.LISpMiner;

namespace SewebarWeb
{
	public class Task : IHttpHandler, IRequiresSessionState
	{
		public void ProcessRequest(HttpContext context)
		{
			context.Response.ContentType = "text/xml";
			
			if (context.Session["LM"] != null && context.Session["LM"] is LISpMiner && context.Request["content"] != null)
			{
				var taskName = "task";

				using (var stream = new StringReader(context.Request["content"]))
				{
					var xpath = new XPathDocument(stream);
					var docNav = xpath.CreateNavigator();

					if (docNav.NameTable != null)
					{
						var nsmgr = new XmlNamespaceManager(docNav.NameTable);
						nsmgr.AddNamespace("guha", "http://keg.vse.cz/ns/GUHA0.1rev1");
						nsmgr.AddNamespace("pmml", "http://www.dmg.org/PMML-4_0");

						var node = docNav.SelectSingleNode("/pmml:PMML/guha:AssociationModel/@modelName", nsmgr);
						taskName = node != null ? node.Value : taskName;
					}
				}

				var taskXmlPath = String.Format("{0}/xml/{1:yyyyMMdd-Hmmss}_task.pmml",
				                                AppDomain.CurrentDomain.GetData("DataDirectory"), DateTime.Now);

				using (var file = new StreamWriter(taskXmlPath))
				{
					file.Write(context.Request["content"]);
				}

				var importer = ((LISpMiner)context.Session["LM"]).Importer;
				importer.Input = taskXmlPath;
				importer.Alias = String.Format(@"{0}\Sewebar\Template\LM.PMML.Alias.ARD.txt", importer.LMPath);
				importer.Launch();
				
				var task4FtGen = ((LISpMiner)context.Session["LM"]).Task4FtGen;
				task4FtGen.TaskName = taskName;
				task4FtGen.Launch();
				
				var exporter = ((LISpMiner)context.Session["LM"]).Exporter;
				exporter.Output = String.Format("{0}/xml/{1:yyyyMMdd-Hmmss}_results.xml", AppDomain.CurrentDomain.GetData("DataDirectory"), DateTime.Now);
				exporter.Template = String.Format(@"{0}\Sewebar\Template\ARDExport.LM.Template.txt", exporter.LMPath);
				exporter.Alias = String.Format(@"{0}\Sewebar\Template\LM.PMML.Alias.ARD.txt", exporter.LMPath);
				exporter.TaskName = taskName;
				exporter.Launch();
				
				context.Response.WriteFile(exporter.Output);
			}
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