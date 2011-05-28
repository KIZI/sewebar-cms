using System;
using System.IO;
using System.Text.RegularExpressions;
using System.Web;
using System.Web.SessionState;
using System.Xml;
using System.Xml.XPath;
using LMWrapper;
using LMWrapper.LISpMiner;
using log4net;

namespace SewebarWeb
{
	public class Task : IHttpHandler, IRequiresSessionState
	{
		private static readonly ILog Log = LogManager.GetLogger(typeof(Task));
		private static readonly string InvalidChars = string.Format(@"[{0}]+", Regex.Escape(new string(Path.GetInvalidFileNameChars())));

		public bool IsReusable
		{
			get
			{
				return false;
			}
		}

		protected string GetTaskName(string xml)
		{
			String taskName = null;

			using (var stream = new StringReader(xml))
			{
				var xpath = new XPathDocument(stream);
				var docNav = xpath.CreateNavigator();

				if (docNav.NameTable != null)
				{
					var nsmgr = new XmlNamespaceManager(docNav.NameTable);
					nsmgr.AddNamespace("guha", "http://keg.vse.cz/ns/GUHA0.1rev1");
					nsmgr.AddNamespace("pmml", "http://www.dmg.org/PMML-4_0");

					var node = docNav.SelectSingleNode("/pmml:PMML/guha:AssociationModel/@modelName", nsmgr);
					taskName = node != null ? node.Value : null;
				}
			}

			return taskName;
		}

		protected string GetStatus(string xmlPath)
		{
			var xpath = new XPathDocument(xmlPath);
			var docNav = xpath.CreateNavigator();

			var node = docNav.SelectSingleNode("//@taskState");
			return node != null ? node.Value : null;
		}

		public void ProcessRequest(HttpContext context)
		{
			var miner = (context.Session["LM"] as LISpMiner);
			var content = context.Request["content"];
			var dataFolder = String.Format("{1}/xml/{0}", miner != null ? miner.Id : String.Empty, AppDomain.CurrentDomain.GetData("DataDirectory"));

			context.Response.ContentType = "text/xml";

			if (miner != null && content != null)
			{
				if (!Directory.Exists(dataFolder)) Directory.CreateDirectory(dataFolder);

				// get task name from importing task XML
				var taskName = this.GetTaskName(content) ?? "task";
				var taskFileName = Regex.Replace(taskName, InvalidChars, "_");
				var taskXmlPath = String.Format("{0}/task_{1}_{2:yyyyMMdd-Hmmss}.xml", dataFolder, taskFileName, DateTime.Now);

				// save importing task XML
				using (var file = new StreamWriter(taskXmlPath))
				{
					file.Write(content);
				}

				// TODO: parallel runnig - because of _AppLog.dat
				//if (miner.Status != ExecutableStatus.Ready) throw new LISpMinerException("Parallel runnig not supported");

				// try to export results
				var exporter = miner.Exporter;
				exporter.Output = String.Format("{0}/results_{1}_{2:yyyyMMdd-Hmmss}.xml", dataFolder, taskFileName, DateTime.Now);
				exporter.Template = String.Format(@"{0}\Sewebar\Template\ARDExport.LM.Template.txt", exporter.LMPath);
				//exporter.Template = String.Format(@"{0}\Sewebar\Template\4ftMiner.Task.PMML.Template.txt", exporter.LMPath);
				exporter.Alias = String.Format(@"{0}\Sewebar\Template\LM.PMML.Alias.ARD.txt", exporter.LMPath);
				exporter.TaskName = taskName;
				//exporter.TaskId = "13";

				try
				{
					exporter.Execute();
				}
				catch (LISpMinerException ex)
				{
					Log.Debug(ex);

					// import task
					var importer = miner.Importer;
					importer.Input = taskXmlPath;
					importer.Alias = String.Format(@"{0}\Sewebar\Template\LM.PMML.Alias.ARD.txt", importer.LMPath);
					importer.Execute();

					// run export once again to get results from newly imported task
					exporter.Execute();
				}

				var status = this.GetStatus(exporter.Output);

				// run task - generate results
				if (status == "Not generated")
				{
					if (miner.Task4FtGen.Status == ExecutableStatus.Ready)
					{
						var task4FtGen = miner.Task4FtGen;
						task4FtGen.TaskName = taskName;
						//task4FtGen.TaskId = "13";
						task4FtGen.Execute();

						// run export once again to refresh results and status
						exporter.Execute();
					}
					else
					{
						Log.Debug("Waiting for result generation");
					}
				}
				else
				{
					Log.DebugFormat("taskState is {0} - not regenerating", status);
				}

				// write results to response
				if (File.Exists(exporter.Output))
				{
					context.Response.WriteFile(exporter.Output);
				}
				else
				{
					throw new LISpMinerException("Results generation did not succeed.");
				}
			}
		}
	}
}