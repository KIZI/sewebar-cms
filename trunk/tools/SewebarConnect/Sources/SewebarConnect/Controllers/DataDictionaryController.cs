﻿using System;
using System.Web.Mvc;
using SewebarConnect.API;
using SewebarConnect.API.Requests.DataDictionary;
using SewebarConnect.API.Responses.DataDictionary;

namespace SewebarConnect.Controllers
{
	[APIErrorHandler]
	public class DataDictionaryController : ApiBaseController
	{
		private XmlResult Import()
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

				response.Message = String.Format("Data Dictionary imported to {0}", importer.LISpMiner.Id);
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
		public ExportResponse Get()
		{
			var request = new ExportRequest(this);

			var response = new ExportResponse();

			var exporter = this.LISpMiner.Exporter;
			exporter.NoAttributeDisctinctValues = true;
			exporter.NoEscapeSeqUnicode = true;
			exporter.MatrixName = request.MatrixName;
			exporter.Output = String.Format("{0}/results_{1}_{2:yyyyMMdd-Hmmss}.xml", request.DataFolder, "DD", DateTime.Now);
			exporter.Template = String.Format(@"{0}\Sewebar\Template\{1}", exporter.LMPath,
											  request.GetTemplate("LMDataSource.Matrix.ARD.Template.PMML"));
			exporter.Execute();

			response.Status = Status.Success;
			response.OutputFilePath = exporter.Output;

			return response;
		}
	}
}