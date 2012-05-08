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

			return new XmlResult
			       	{
			       		Data = new ExceptionResponse("No DataDictionary given.")
			       	};
		}
	}
}
