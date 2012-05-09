using System;
using System.Web.Mvc;
using SewebarConnect.API;
using SewebarConnect.API.Requests.DataDictionary;
using SewebarConnect.API.Responses;
using SewebarConnect.API.Responses.DataDictionary;

namespace SewebarConnect.Controllers
{
	public class DataDictionaryController : BaseController
	{
		[ValidateInput(false)]
		[ErrorHandler]
		public XmlResult Import()
		{
			var request = new ImportRequest(this);

			var response = new ImportResponse
			               	{
			               		Id = this.LISpMiner.Id
			               	};

			if (this.LISpMiner != null && request.DataDictionary != null)
			{
				var importer = this.LISpMiner.Importer;
				importer.Input = request.DataDictionaryPath;
				importer.Execute();

				response.Message = String.Format("Imported {0} to {1}", importer.Input, importer.Dsn);
				response.Status = Status.Success;

				return new XmlResult
				       	{
				       		Data = response
				       	};
			}

			throw new Exception("No DataDictionary given.");
		}

		[ValidateInput(false)]
		[ErrorHandler]
		public ActionResult Export()
		{
			var request = new ExportRequest(this);

			var response = new ExportResponse();

			var exporter = this.LISpMiner.Exporter;
			exporter.MatrixName = request.MatrixName;
			exporter.Output = String.Format("{0}/results_{1}_{2:yyyyMMdd-Hmmss}.xml", request.DataFolder, "DD", DateTime.Now);
			exporter.Template = String.Format(@"{0}\Sewebar\Template\{1}", exporter.LMPath,
			                                  request.GetTemplate("4ftMiner.Task.Template.PMML"));
			exporter.Execute();

			response.Status = Status.Success;
			response.OutputFilePath = exporter.Output;

			return new XmlFileResult
			       	{
			       		Data = response
			       	};
		}
	}
}
