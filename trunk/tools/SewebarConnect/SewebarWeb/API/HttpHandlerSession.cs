using System;
using System.Web.SessionState;
using LMWrapper.LISpMiner;

namespace SewebarWeb.API
{
	public class HttpHandlerSession : HttpHandler, IRequiresSessionState
	{
		private const string PARAMS_GUID = "guid";
		private const string SESSION_KEY = "LM";
		private LISpMiner _miner;

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
					if (this.Context == null)
					{
						// we need a context
						throw new Exception("Ensure to call base.ProcessRequest");
					}

					var guid = this.Context.Request.Params[PARAMS_GUID];
					var sessionMiner = this.Context.Session[SESSION_KEY] as LISpMiner;

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
							var id = Context.Session.SessionID;
							var db = String.Format(@"{0}\Barbora.mdb", AppDomain.CurrentDomain.GetData("DataDirectory"));

							this._miner = new LISpMiner(Global.Environment, id, db, string.Empty);
							Global.RegisterSessionMiner(this._miner);
							this.Context.Session[SESSION_KEY] = this._miner;
						}
					}
				}

				return this._miner;
			}
		}
	}
}