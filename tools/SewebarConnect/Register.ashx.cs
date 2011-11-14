using System;
using System.Web;
using LMWrapper;
using LMWrapper.LISpMiner;
using LMWrapper.ODBC;
using Newtonsoft.Json;
using log4net;

namespace SewebarWeb
{
	/// <summary>
	/// Summary description for Register
	/// </summary>
	public class Register : IHttpHandler
	{
		private static readonly ILog Log = LogManager.GetLogger(typeof(Register));

		#region Properties

		public bool IsReusable
		{
			get
			{
				return false;
			}
		}

		#endregion

		public void ProcessRequest(HttpContext context)
		{
			OdbcDrivers conn;

			//context.Response.ContentType = "application/json";
			context.Response.ContentType = "text/plain";

			var type = context.Request.Params["type"];

			if (Enum.TryParse(type, true, out conn))
			{
				try
				{
					var id = ShortGuid.NewGuid();
					var database = OdbcConnection.Create(conn, Global.Environment, id.ToString(), context.Request.Params);

					Global.Environment.Register(new LISpMiner(Global.Environment, id.ToString(), database));

					var result = new { Status = "success", Name = id.Value };
					context.Response.Write(JsonConvert.SerializeObject(result));
				}
				catch (Exception ex)
				{
					Log.Error(ex);

					var result = new { Status = "failure", Reason = ex.Message };
					context.Response.Write(JsonConvert.SerializeObject(result));
				}
			}
			else
			{
				const string message = "Database was not correctly defined (type).";

				Log.Error(message);

				var result = new { Status = "failure", Reason = message };
				context.Response.Write(JsonConvert.SerializeObject(result));
			}
		}
	}
}