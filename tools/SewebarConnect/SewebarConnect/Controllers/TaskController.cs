using System;
using System.Web.Mvc;
using LMWrapper;
using LMWrapper.LISpMiner;
using SewebarConnect.API;
using SewebarConnect.API.Requests.Task;
using SewebarConnect.API.Responses.Task;
using log4net;

namespace SewebarConnect.Controllers
{
    public class TaskController : BaseController
    {
		private static readonly ILog Log = LogManager.GetLogger(typeof(TaskController));
		private const string DefaultTemplate = "ETreeMiner.Task.Template.PMML";

		[ValidateInput(false)]
		[ErrorHandler]
        public ActionResult Export()
        {
			LMSwbExporter exporter = this.LISpMiner.Exporter;

			try
			{
				var request = new TaskInfoRequest(this);
				var response = new TaskResponse();

				if (this.LISpMiner != null && request.TaskName != null)
				{
					exporter.Output = String.Format("{0}/results_{1}_{2:yyyyMMdd-Hmmss}.xml", request.DataFolder,
					                                request.TaskFileName, DateTime.Now);

					exporter.Template = String.Format(@"{0}\Sewebar\Template\{1}", exporter.LMPath,
					                                  request.GetTemplate(DefaultTemplate));

					exporter.TaskName = request.TaskName;
					exporter.NoEscapeSeqUnicode = true;

					// try to export results
					exporter.Execute();

					if (!System.IO.File.Exists(exporter.Output))
					{
						throw new LISpMinerException("Results generation did not succeed. Task possibly does not exist but no appLog generated.");
					}

					response.OutputFilePath = exporter.Output;
				}
				else
				{
					throw new Exception("No LISpMiner instance or task defined.");
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
			}
        }
    }
}
