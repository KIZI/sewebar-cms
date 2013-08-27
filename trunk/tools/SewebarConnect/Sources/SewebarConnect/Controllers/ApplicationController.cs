using System;
using System.IO;
using System.Web.Mvc;
using LMWrapper.LISpMiner;
using SewebarConnect.API;

namespace SewebarConnect.Controllers
{
	public class ApplicationController : Controller
	{
		protected const string PARAMS_GUID = "guid";

		private LISpMiner _miner;

		protected LISpMiner LISpMiner
		{
			get
			{
				if (this._miner == null)
				{
					if (this.HttpContext == null)
					{
						// we need a context
						throw new Exception("Ensure to call base.ProcessRequest");
					}

					var guid = this.RouteData.Values[PARAMS_GUID] as string;

					if (guid == null)
					{
						throw new Exception(String.Format("Not specified which LISpMiner to work with."));
					}

					if (!MvcApplication.Environment.Exists(guid))
					{
						throw new Exception(String.Format("Requested LISpMiner with ID {0}, does not exists", guid));
					}

					this._miner = MvcApplication.Environment.GetMiner(guid);
				}

				return this._miner;
			}
		}

		public ActionResult Index()
		{
			return View(SVNDataAttribute.AssemblySVNData);
		}

		public ActionResult Update()
		{
			string path;

			if (this.HttpContext.Request.Params[PARAMS_GUID] != null)
			{
				throw new NotSupportedException();

				// path = Path.GetFullPath(this.LISpMiner.LMPath);
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

		#region Miners

		public ActionResult Miners()
		{
			return View();
		}

		public ActionResult Miner()
		{
			return View(this.LISpMiner);
		}

		[ErrorHandler]
		public ActionResult Remove()
		{
			MvcApplication.Environment.Unregister(this.LISpMiner);

			return new XmlResult
			{
				Data = new Response { Status = Status.Success, Message = "LISpMiner removed." }
			};
		}

		#endregion
	}
}
