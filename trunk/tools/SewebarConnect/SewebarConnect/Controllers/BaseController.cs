using System;
using System.Web;
using System.Web.Mvc;
using LMWrapper.LISpMiner;

namespace SewebarConnect.Controllers
{
	public class BaseController : Controller
	{
		private const string PARAMS_GUID = "guid";
		private const string SESSION_KEY = "LM";

		private LISpMiner _miner;

		public LISpMiner Miner
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
					var sessionMiner = this.HttpContext.Session[SESSION_KEY] as LISpMiner;

					// If request contains guid, try to find registered miner
					if (guid != null)
					{
						LISpMiner miner = MvcApplication.Environment.GetMiner(guid);

						// Dispose possible existing session miner
						if (sessionMiner != null && miner != null)
						{
							sessionMiner.Dispose();
						}

						this._miner = miner;
					}

					// If no registered miner asked for or found try to use session
					if (this._miner == null)
					{
						if (sessionMiner != null)
						{
							this._miner = sessionMiner;
						}
						else
						{
							var id = HttpContext.Session.SessionID;
							var db = String.Format(@"{0}\Barbora.mdb", AppDomain.CurrentDomain.GetData("DataDirectory"));

							this._miner = new LISpMiner(MvcApplication.Environment, id, db);
							MvcApplication.RegisterSessionMiner(this._miner);
							this.HttpContext.Session[SESSION_KEY] = this._miner;
						}
					}
				}

				return this._miner;
			}
		}
	}
}