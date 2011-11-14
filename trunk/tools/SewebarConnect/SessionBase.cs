using System;
using System.Web;
using System.Web.SessionState;
using LMWrapper.LISpMiner;
using LMWrapper.ODBC;

namespace SewebarWeb
{
	public class SessionBase : IHttpHandler, IRequiresSessionState
	{
		private const string PARAMS_GUID = "guid";
		private const string SESSION_KEY = "LM";
		private LISpMiner _miner;
		private HttpContext _context;

		public static void Clean(HttpSessionState session)
		{
			var miner = session[SESSION_KEY] as IDisposable;

			if (miner != null)
			{
				miner.Dispose();
			}	
		}

		protected LISpMiner Miner
		{
			get
			{
				if (this._miner == null)
				{
					if (this._context == null)
					{
						// we need a context
						throw new Exception("Ensure to call base.ProcessRequest");
					}

					var guid = this._context.Request.Params[PARAMS_GUID];
					var sessionMiner = this._context.Session[SESSION_KEY] as LISpMiner;

					// If request contains guid, try to find registered miner
					if (guid != null)
					{
						LISpMiner miner = Global.Environment.GetMiner(guid);

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
							var id = _context.Session.SessionID;
							var db = String.Format(@"{0}\Barbora.mdb", AppDomain.CurrentDomain.GetData("DataDirectory"));

							this._miner = new LISpMiner(Global.Environment, id, db);
							Global.RegisterSessionMiner(this._miner);
							this._context.Session[SESSION_KEY] = this._miner;
						}
					}
				}

				return this._miner;
			}
		}

		public bool IsReusable
		{
			get
			{
				return false;
			}
		}

		public virtual void ProcessRequest(HttpContext context)
		{
			this._context = context;
		}
	}
}