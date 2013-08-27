using System;
using LMWrapper.LISpMiner;
using SewebarConnect.API;
using SewebarConnect.API.Requests.DataDictionary;
using SewebarConnect.API.Responses.DataDictionary;

namespace SewebarConnect.Controllers
{
	[APIErrorHandler]
	public class DataDictionaryController : ApiBaseController
	{
		public ExportResponse Get()
		{
			var request = new ExportRequest(this);

			var response = new ExportResponse();

			var exporter = this.LISpMiner.Exporter;
			exporter.NoAttributeDisctinctValues = true;
			exporter.NoEscapeSeqUnicode = true;
			exporter.MatrixName = request.MatrixName;
			exporter.Output = String.Format("{0}/results_{1}_{2:yyyyMMdd-Hmmss}.xml", request.DataFolder, "DD", DateTime.Now);
			exporter.Template = String.Format(@"{0}\Sewebar\Template\{1}", exporter.LMExecutablesPath,
											  request.GetTemplate("LMDataSource.Matrix.ARD.Template.PMML"));
			exporter.Execute();

			response.Status = Status.Success;
			response.OutputFilePath = exporter.Output;

			return response;
		}

		public ImportResponse Put()
		{
			var request = new ImportRequest(this);

			var response = new ImportResponse
				{
					Id = this.LISpMiner.Id
				};

			if (this.LISpMiner != null && request.DataDictionary != null)
			{
				LMSwbImporter importer = this.LISpMiner.Importer;
				importer.Input = request.DataDictionaryPath;
				importer.NoCheckPrimaryKeyUnique = false;
				importer.Execute();

				response.Message = String.Format("Data Dictionary imported to {0}", importer.LISpMiner.Id);
				response.Status = Status.Success;

				return response;
			}

			throw new Exception("No DataDictionary given.");
		}
	}
}
