using System;
using System.Collections.Generic;
using LMWrapper.LISpMiner;
using log4net;

namespace SewebarWeb
{
	public class Global : System.Web.HttpApplication
	{
		private static readonly ILog Log = LogManager.GetLogger(typeof(Global));
		private static LMWrapper.Environment _env;
		private static Dictionary<string, LISpMiner> _registeredSessionMiners;

		#region Properties

		public static LMWrapper.Environment Environment
		{
			get
			{
				if(_env == null)
				{
					var appData = AppDomain.CurrentDomain.GetData("DataDirectory").ToString();

					_env = new LMWrapper.Environment
					{
						DataPath = appData,
						LMPoolPath = String.Format(@"{0}", @"C:\LMs\"),
						LMPath = String.Format(@"{0}\Libs\{1}", AppDomain.CurrentDomain.BaseDirectory, "LISp Miner"),
					};
				}

				return _env;
			}
		}

		protected static Dictionary<string, LISpMiner> RegisteredSessionMiners
		{
			get
			{
				if (_registeredSessionMiners == null)
				{
					_registeredSessionMiners = new Dictionary<string, LISpMiner>();
				}

				return _registeredSessionMiners;
			}
		}

		public static Dictionary<string, LISpMiner>.KeyCollection ExistingSessionMiners
		{
			get { return RegisteredSessionMiners.Keys; }
		}

		public static string RegisterSessionMiner(LISpMiner miner)
		{
			RegisteredSessionMiners.Add(miner.Id, miner);

			return miner.Id;
		}

		#endregion

		protected void Application_Start(object sender, EventArgs e)
		{
			// Load logging info
			log4net.Config.XmlConfigurator.Configure();
		}

		protected void Session_Start(object sender, EventArgs e)
		{
		
		}

		protected void Application_BeginRequest(object sender, EventArgs e)
		{

		}

		protected void Application_AuthenticateRequest(object sender, EventArgs e)
		{

		}

		protected void Application_Error(object sender, EventArgs e)
		{
			Log.Error(Server.GetLastError());
		}

		protected void Session_End(object sender, EventArgs e)
		{
			if (RegisteredSessionMiners.ContainsKey(Session.SessionID))
			{
				RegisteredSessionMiners.Remove(Session.SessionID);
			}

			SessionBase.Clean(Session);
		}

		protected void Application_End(object sender, EventArgs e)
		{
			
		}
	}
}