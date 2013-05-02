using System;
using System.IO;
using System.Linq;
using System.Web.Mvc;
using LMWrapper;
using LMWrapper.LISpMiner;
using LMWrapper.ODBC;
using SewebarConnect.API;
using SewebarConnect.API.Requests.Application;
using SewebarConnect.API.Responses.Application;

namespace SewebarConnect.Controllers
{
	public class ApplicationController : BaseController
	{
		public ActionResult Index()
		{
			return View();
		}

		public ActionResult Update()
		{
			string path;

			if (this.HttpContext.Request.Params[PARAMS_GUID] != null)
			{
				throw new NotSupportedException();

				path = Path.GetFullPath(this.LISpMiner.LMPath);
			}
			else
			{
				path = Path.GetFullPath(MvcApplication.Environment.LMPath + "\\..");
			}

			var manager = new LMWrapper.Manager(path);

			ViewBag.Output = manager.Update();

			ViewBag.Path = path;

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
		[ErrorHandler]
		public XmlResult Register()
		{
			var request = new RegistrationRequest(this.HttpContext);
			var id = ShortGuid.NewGuid();
			var miner = new LISpMiner(MvcApplication.Environment, id.ToString(), request.DbConnection, request.Metabase);

			MvcApplication.Environment.Register(miner);

			return new XmlResult
			       	{
			       		Data = new RegistrationResponse {Id = id}
			       	};
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
