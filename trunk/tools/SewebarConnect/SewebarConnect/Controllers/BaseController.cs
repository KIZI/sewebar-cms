using System;
using System.Web;
using System.Web.Mvc;
using LMWrapper.LISpMiner;

namespace SewebarConnect.Controllers
{
	public class BaseController : Controller
	{
		protected const string PARAMS_GUID = "guid";

		private LISpMiner _miner;

		public LISpMiner LISpMiner
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

					var guid = this.HttpContext.Request.Params[PARAMS_GUID];

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
	}
}