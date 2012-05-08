using System;
using System.Linq;
using System.Web.Mvc;
using LMWrapper;
using LMWrapper.LISpMiner;
using LMWrapper.ODBC;
using SewebarConnect.API;
using SewebarConnect.API.Requests.Application;
using SewebarConnect.API.Responses;
using SewebarConnect.API.Responses.Application;
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

		public ActionResult Miners()
		{
			return View();
		}

		public ActionResult Miner()
		{
			var acceptTypes = this.HttpContext.Request.AcceptTypes;

			if (acceptTypes != null &&
			    (acceptTypes.Contains("text/xml") ||
			     acceptTypes.Contains("application/xml")))
			{
				return new XmlResult
				       	{
				       		Data = new LISpMinerResponse(this.LISpMiner)
				       	};
			}

			return View(this.LISpMiner);
		}

		[ErrorHandler]
		public ActionResult Remove()
		{
			MvcApplication.Environment.Unregister(this.LISpMiner);

			return new XmlResult
			       	{
			       		Data = new Response {Status = Status.Success, Message = "LISpMiner removed."}
			       	};
		}

		[HttpPost]
		public XmlResult Register()
		{
			try
			{
				var request = new RegistrationRequest(this);
				var id = ShortGuid.NewGuid();
				var database = OdbcConnection.Create(MvcApplication.Environment, id.ToString(), request.DbConnection);
				var miner = new LISpMiner(MvcApplication.Environment, id.ToString(), database, request.Metabase);

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

		[ErrorHandler]
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

			throw new Exception(String.Format("Not existing DSN: {0}", dsn));
		}
	}
}
