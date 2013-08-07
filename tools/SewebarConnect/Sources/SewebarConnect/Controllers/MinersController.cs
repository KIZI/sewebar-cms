using System;
using System.Web;
using LMWrapper;
using LMWrapper.LISpMiner;
using SewebarConnect.API;
using SewebarConnect.API.Requests.Application;
using SewebarConnect.API.Responses.Application;

namespace SewebarConnect.Controllers
{
	[APIErrorHandler]
	public class MinersController : ApiBaseController
	{
		public LISpMinerResponse Get()
		{
			try
			{
				var lm = this.LISpMiner;

				// one miner
				return new LISpMinerResponse(lm);
			}
			catch (Exception)
			{
				// all miners
				return new LISpMinerResponse();
			}
		}

		public RegistrationResponse Post()
		{
			var request = new RegistrationRequest();
			var id = ShortGuid.NewGuid();
			var miner = new LISpMiner(MvcApplication.Environment, id.ToString(), request.DbConnection, request.Metabase, true);

			MvcApplication.Environment.Register(miner);

			var response = new RegistrationResponse {Id = id};

			return response;
		}

		public Response Delete()
		{
			MvcApplication.Environment.Unregister(this.LISpMiner);

			return new Response {Status = Status.Success, Message = "LISpMiner removed."};
		}
	}
}
