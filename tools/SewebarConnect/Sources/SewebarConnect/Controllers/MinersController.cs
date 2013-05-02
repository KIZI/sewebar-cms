using System;
using System.Web;
using LMWrapper;
using LMWrapper.LISpMiner;
using SewebarConnect.API;
using SewebarConnect.API.Requests.Application;
using SewebarConnect.API.Requests.DataDictionary;
using SewebarConnect.API.Responses.Application;
using SewebarConnect.API.Responses.DataDictionary;

namespace SewebarConnect.Controllers
{
	[APIErrorHandler]
	public class MinersController : ApiBaseController
	{
		public string Get()
		{
			return "All registered miner accessible for current user";
		}

		public RegistrationResponse Post()
		{
			var request = new RegistrationRequest(HttpContext.Current);
			var id = ShortGuid.NewGuid();
			var miner = new LISpMiner(MvcApplication.Environment, id.ToString(), request.DbConnection, request.Metabase);

			MvcApplication.Environment.Register(miner);

			var response = new RegistrationResponse {Id = id};

			return response;
		}

		public ImportResponse Patch()
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

				return response;
			}

			throw new Exception("No DataDictionary given.");
		}
	}
}
