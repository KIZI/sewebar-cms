using System;
using System.Web;
using LMWrapper;
using LMWrapper.LISpMiner;
using LMWrapper.ODBC;
using Newtonsoft.Json;
using SewebarWeb.API;
using log4net;

namespace SewebarWeb
{
	public class Register : HttpHandler
	{
		private static readonly ILog Log = LogManager.GetLogger(typeof (Register));

		private API.RegisterResponse Response { get; set; }

		private API.RegisterRequest Request { get; set; }

		public override void ProcessRequest(HttpContext context)
		{
			this.Request = new API.RegisterRequest(null, context);

			this.Response = new API.RegisterResponse(context);

			try
			{
				var id = ShortGuid.NewGuid();
				var database = OdbcConnection.Create(this.Request.Connection, Global.Environment, id.ToString(),
				                                     this.Request.Parameters);
				var miner = new LISpMiner(Global.Environment, id.ToString(), database);

				Global.Environment.Register(miner);

				this.Response.Status = Status.success;
				this.Response.Id = id.Value;
			}
			catch (Exception ex)
			{
				Log.Error(ex);

				this.Response.Status = Status.failure;
				this.Response.Message = ex.Message;
			}
			finally
			{
				this.Response.Write();
			}
		}
	}
}