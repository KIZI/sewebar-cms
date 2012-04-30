using System;
using System.Web.Mvc;
using LMWrapper;
using LMWrapper.LISpMiner;
using LMWrapper.ODBC;
using SewebarConnect.API;
using log4net;

namespace SewebarConnect.Controllers
{
	public class ApplicationController : BaseController
	{
		private static readonly ILog Log = LogManager.GetLogger(typeof (ApplicationController));

		public ActionResult Index()
		{
			return View();
		}

		[HttpPost]
		public XmlResult Register()
		{
			try
			{
				var request = new RegistrationRequest(this);
				var id = ShortGuid.NewGuid();
				var database = OdbcConnection.Create(MvcApplication.Environment, id.ToString(), request.DbConnection);
				var miner = new LISpMiner(MvcApplication.Environment, id.ToString(), database);

				MvcApplication.Environment.Register(miner);

				return new XmlResult
				       	{
				       		Data = new RegistrationResponse {Id = id}
				       	};
			}
			catch (Exception ex)
			{
				Log.Error(ex);

				return new XmlResult
				       	{
				       		Data = new ExceptionResponse(ex.Message)
				       	};
			}
		}

		public XmlResult RemoveDsn()
		{
			var dsn = this.HttpContext.Request["dsn"];

			if (ODBCManagerRegistry.DSNExists(dsn))
			{

				ODBCManagerRegistry.RemoveDSN(dsn);

				return new XmlResult
				       	{
				       		Data = new Response {Message = String.Format("Deleted DSN: {0}", dsn)}
				       	};
			}

			return new XmlResult
			       	{
			       		Data = new ExceptionResponse(String.Format("Not existing DSN: {0}", dsn))
			       	};
		}
	}
}
