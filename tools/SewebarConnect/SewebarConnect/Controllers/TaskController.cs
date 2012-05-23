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
using SewebarConnect.API.Exceptions;
using SewebarConnect.API.Requests.Task;
using SewebarConnect.API.Responses.Task;
using log4net;

namespace SewebarConnect.Controllers
{
	public class TaskController : BaseController
	{
		private static readonly ILog Log = LogManager.GetLogger(typeof(TaskController));

		private const string XPathStatus = "/*/*/TaskSetting/Extension/TaskState";
		private const string XPathNumberOfRules = "/*/*/@numberOfRules";
		private const string XPathHypothesesCountMax = "/*/*/TaskSetting/Extension/HypothesesCountMax";

		protected class TaskDefinition
		{
			public string DefaultTemplate { get; set; }

			public ITaskLauncher Launcher { get; set; }
		}

		protected void GetInfo(string xmlPath, out string status, out int numberOfRules, out int hypothesesCountMax)
		{
			var document = XDocument.Load(xmlPath);

			var statusAttribute = ((IEnumerable<object>) document.XPathEvaluate(XPathStatus)).FirstOrDefault() as XElement;
			var numberOfRulesAttribute = ((IEnumerable<object>)document.XPathEvaluate(XPathNumberOfRules)).FirstOrDefault() as XAttribute;
			var hypothesesCountMaxAttribute = ((IEnumerable<object>)document.XPathEvaluate(XPathHypothesesCountMax)).FirstOrDefault() as XElement;

			if (statusAttribute == null)
			{
				throw new InvalidTaskResultXml("TaskState cannot be resolved.", xmlPath, XPathStatus);
			}

			status = statusAttribute.Value;

			if (numberOfRulesAttribute == null || !Int32.TryParse(numberOfRulesAttribute.Value, out numberOfRules))
			{
				throw new InvalidTaskResultXml("NumberOfRulesAttribute cannot be resolved.", xmlPath, XPathNumberOfRules);
			}

			if (hypothesesCountMaxAttribute == null || !Int32.TryParse(hypothesesCountMaxAttribute.Value, out hypothesesCountMax))
			{
				throw new InvalidTaskResultXml("HypothesesCountMax cannot be resolved.", xmlPath, XPathHypothesesCountMax);
			}
		}

		protected ActionResult RunTask(TaskDefinition definition)
		{
			var exporter = this.LISpMiner.Exporter;
			var importer = this.LISpMiner.Importer;

			try
			{
				var request = new TaskRequest(this);
				var response = new TaskResponse();

				if (this.LISpMiner != null && request.Task != null)
				{
					var status = "Not generated";
					var numberOfRules = 0;
					var hypothesesCountMax = Int32.MaxValue;

					exporter.Output = String.Format("{0}/results_{1}_{2:yyyyMMdd-Hmmss}.xml", request.DataFolder,
													request.TaskFileName, DateTime.Now);

					exporter.Template = String.Format(@"{0}\Sewebar\Template\{1}", exporter.LMPath,
													  request.GetTemplate(definition.DefaultTemplate));

					exporter.TaskName = request.TaskName;

					try
					{
						// try to export results
						exporter.Execute();

						if (!System.IO.File.Exists(exporter.Output))
						{
							throw new LISpMinerException("Task possibly does not exist but no appLog generated");
						}
						
						this.GetInfo(exporter.Output, out status, out numberOfRules, out hypothesesCountMax);
					}
					catch (LISpMinerException ex)
					{
						// task was never imported - does not exists. Therefore we need to import at first.
						Log.Debug(ex);

						// import task
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
							if (definition.Launcher.Status == ExecutableStatus.Ready)
							{
								var taskLauncher = definition.Launcher;
								taskLauncher.TaskName = request.TaskName;
								taskLauncher.Execute();

								// run export once again to refresh results and status
								if (status != "Interrupted")
								{
									exporter.Execute();
								}
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
							definition.Launcher.KeepAlive = 10;
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
			finally
			{
				// clean up
				exporter.Output = String.Empty;
				exporter.Template = String.Empty;
				exporter.TaskName = String.Empty;

				importer.Input = String.Empty;
				importer.Alias = String.Empty;
			}
		}

		[ValidateInput(false)]
		[ErrorHandler]
		public ActionResult Run()
		{
			return this.RunTask(new TaskDefinition { DefaultTemplate = "4ftMiner.Task.Template.PMML", Launcher = this.LISpMiner.Task4FtGen });
		}

		[ValidateInput(false)]
		[ErrorHandler]
		public ActionResult Pool()
		{
			return this.RunTask(new TaskDefinition { DefaultTemplate = "ETreeMiner.Task.Template.PMML", Launcher = this.LISpMiner.LMTaskPooler });
		}
	}
}
