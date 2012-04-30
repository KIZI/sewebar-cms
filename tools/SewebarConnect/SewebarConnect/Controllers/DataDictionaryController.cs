using System;
using System.Web.Mvc;
using SewebarConnect.API;

namespace SewebarConnect.Controllers
{
	public class DataDictionaryController : BaseController
	{
		[ValidateInput(false)]
		public XmlResult Import()
		{
			var request = new API.ImportRequest(this);

			var response = new API.ImportResponse
			               	{
			               		Id = this.Miner.Id
			               	};

			if (this.Miner != null && request.DataDictionary != null)
			{
				var importer = this.Miner.Importer;
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
