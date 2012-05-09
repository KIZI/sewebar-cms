using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using System.Xml.Linq;
using System.Xml.XPath;
using LMWrapper;
using LMWrapper.LISpMiner;
using SewebarConnect.API;
using SewebarConnect.API.Requests.Task;
using SewebarConnect.API.Responses;
using SewebarConnect.API.Responses.Task;
using log4net;

namespace SewebarConnect.Controllers
{
	public class TaskController : BaseController
	{
		private static readonly ILog Log = LogManager.GetLogger(typeof(TaskController));

		protected string GetStatus(string xmlPath)
		{
			var document = XDocument.Load(xmlPath);
			var attr = ((IEnumerable<object>) document.XPathEvaluate("//@taskState")).FirstOrDefault() as XAttribute;

			if (attr == null)
			{
				Log.DebugFormat("TaskState cannot be resolved Miner: {0}, Task: {1}.", this.LISpMiner.Id, Path.GetFileName(xmlPath));
				//throw new Exception("TaskState cannot be resolved");
			}

			return (attr != null) ? attr.Value : string.Empty;
		}

		[ValidateInput(false)]
		[ErrorHandler]
		public ActionResult Run()
		{
			var request = new TaskRequest(this);

			var response = new TaskResponse();

			if (this.LISpMiner != null && request.Task != null)
			{
				var status = "Not generated";

				var exporter = this.LISpMiner.Exporter;
				exporter.Output = String.Format("{0}/results_{1}_{2:yyyyMMdd-Hmmss}.xml", request.DataFolder,
				                                request.TaskFileName, DateTime.Now);

				exporter.Template = String.Format(@"{0}\Sewebar\Template\{1}", exporter.LMPath,
				                                  request.GetTemplate("4ftMiner.Task.Template.PMML"));
				exporter.TaskName = request.TaskName;

				try
				{
					// try to export results
					exporter.Execute();

					if (!System.IO.File.Exists(exporter.Output))
					{
						throw new LISpMinerException("Task possibly does not exist but no appLog generated");
					}

					status = this.GetStatus(exporter.Output);
				}
				catch (LISpMinerException ex)
				{
					// task was never imported - does not exists. Therefore we need to import at first.
					Log.Debug(ex);

					// import task
					var importer = this.LISpMiner.Importer;
					importer.Input = request.TaskPath;

					if (!string.IsNullOrEmpty(request.Alias))
					{
						importer.Alias = String.Format(@"{0}\Sewebar\Template\{1}", importer.LMPath, request.Alias);
					}

					importer.Execute();
				}

				switch (status)
				{
						// * Not Generated (po zadání úlohy nebo změně v zadání)
					case "Not generated":
						// * Interrupted (přerušena -- buď kvůli time-outu nebo max počtu hypotéz)
					case "Interrupted":
						// run task - generate results
						if (this.LISpMiner.Task4FtGen.Status == ExecutableStatus.Ready)
						{
							var task4FtGen = this.LISpMiner.Task4FtGen;
							task4FtGen.TaskName = request.TaskName;
							task4FtGen.Execute();

							// run export once again to refresh results and status
							if (status != "Interrupted")
								exporter.Execute();
						}
						else
						{
							Log.Debug("Waiting for result generation");
						}
						break;
						// * Running (běží generování)
					case "Running":
						// * Waiting (čeká na spuštění -- pro TaskPooler, zatím neimplementováno)
					case "Waiting":
						this.LISpMiner.Task4FtGen.KeepAlive = 10;
						break;
						// * Solved (úspěšně dokončena)
					case "Solved":
					case "Finnished":
					default:
						break;
				}

				response.OutputFilePath = exporter.Output;

				if (!System.IO.File.Exists(response.OutputFilePath))
				{
					throw new Exception("Results generation did not succeed.");
				}
			}
			else
			{
				throw new Exception("No LISpMiner instance or task defined");
			}

			return new XmlFileResult
			       	{
			       		Data = response
			       	};
		}

		[ValidateInput(false)]
		[ErrorHandler]
		public ActionResult Pool()
		{
			var request = new TaskRequest(this);

			var response = new TaskResponse();

			if (this.LISpMiner != null && request.Task != null)
			{
				var status = "Not generated";

				var exporter = this.LISpMiner.Exporter;
				exporter.Output = String.Format("{0}/results_{1}_{2:yyyyMMdd-Hmmss}.xml", request.DataFolder,
				                                request.TaskFileName, DateTime.Now);

				exporter.Template = String.Format(@"{0}\Sewebar\Template\{1}", exporter.LMPath,
				                                  request.GetTemplate("4ftMiner.Task.Template.PMML"));
				exporter.TaskName = request.TaskName;

				try
				{
					// try to export results
					exporter.Execute();

					if (!System.IO.File.Exists(exporter.Output))
					{
						throw new LISpMinerException("Task possibly does not exist but no appLog generated");
					}

					status = this.GetStatus(exporter.Output);
				}
				catch (LISpMinerException ex)
				{
					// task was never imported - does not exists. Therefore we need to import at first.
					Log.Debug(ex);

					// import task
					var importer = this.LISpMiner.Importer;
					importer.Input = request.TaskPath;

					if (!string.IsNullOrEmpty(request.Alias))
					{
						importer.Alias = String.Format(@"{0}\Sewebar\Template\{1}", importer.LMPath, request.Alias);
					}

					importer.Execute();
				}

				switch (status)
				{
						// * Not Generated (po zadání úlohy nebo změně v zadání)
					case "Not generated":
						// * Interrupted (přerušena -- buď kvůli time-outu nebo max počtu hypotéz)
					case "Interrupted":
						// run task - generate results
						if (this.LISpMiner.LMTaskPooler.Status == ExecutableStatus.Ready)
						{
							var taskPooler = this.LISpMiner.LMTaskPooler;
							taskPooler.TaskName = request.TaskName;
							taskPooler.Execute();

							// run export once again to refresh results and status
							if (status != "Interrupted")
								exporter.Execute();
						}
						else
						{
							Log.Debug("Waiting for result generation");
						}
						break;
						// * Running (běží generování)
					case "Running":
						// * Waiting (čeká na spuštění -- pro TaskPooler, zatím neimplementováno)
					case "Waiting":
						this.LISpMiner.LMTaskPooler.KeepAlive = 10;
						break;
						// * Solved (úspěšně dokončena)
					case "Solved":
					case "Finnished":
					default:
						break;
				}

				response.OutputFilePath = exporter.Output;

				if (!System.IO.File.Exists(response.OutputFilePath))
				{
					throw new Exception("Results generation did not succeed.");
				}
			}
			else
			{
				throw new Exception("No LISpMiner instance or task defined");
			}

			return new XmlFileResult
			       	{
			       		Data = response
			       	};
		}
	}
}
