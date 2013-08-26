using System;
using System.Configuration;
using System.IO;
using System.Web.Http;
using System.Web.Mvc;
using System.Web.Routing;
using LMWrapper.LISpMiner;
using NHibernate;
using NHibernate.Cfg;
using NHibernate.Context;
using SewebarKey.Configurations;
using log4net;

namespace SewebarConnect
{
	public class MvcApplication : System.Web.HttpApplication
	{
		private static readonly ILog Log = LogManager.GetLogger(typeof(MvcApplication));
		private static LMWrapper.Environment _env;
		public static ISessionFactory SessionFactory { get; private set; }

		#region Properties

		public static LMWrapper.Environment Environment
		{
			get
			{
				if (_env == null)
				{
					_env = new LMWrapper.Environment
					{
						DataPath = GetAppSetting("Sewebar-DataPath", String.Format(@"{0}\..\..\Data", AppDomain.CurrentDomain.BaseDirectory)),
						LMPoolPath = GetAppSetting("Sewebar-PoolPath", String.Format(@"{0}\..\..\Data\LMs", AppDomain.CurrentDomain.BaseDirectory)),
						LMPath = GetAppSetting("Sewebar-LMPath", String.Format(@"{0}\..\..\Libs\{1}", AppDomain.CurrentDomain.BaseDirectory, "LISp Miner")),
						PCGridPath = GetAppSetting("Sewebar-PCGridPath", String.Format(@"{0}\..\..\Libs\{1}\Binaries", AppDomain.CurrentDomain.BaseDirectory, "PCGrid")),
						TimeLog = GetAppSetting("Sewebar-TimeLog", false),
					};
				}

				return _env;
			}
		}

		#endregion

		protected static string GetAppSetting(string setting, string defaultValue)
		{
			var value = ConfigurationManager.AppSettings[setting];
			return String.IsNullOrEmpty(value) ? defaultValue : value;
		}

		protected static bool GetAppSetting(string setting, bool defaultValue)
		{
			var value = ConfigurationManager.AppSettings[setting];
			bool parse;

			if (bool.TryParse(value, out parse))
			{
				return parse;
			}

			return defaultValue;
		}

		protected static void RegisterExisting()
		{
			var env = MvcApplication.Environment;

			if (!Directory.Exists(env.LMPoolPath)) return;

			foreach (var path in Directory.GetDirectories(env.LMPoolPath))
			{
				try
				{
					var directory = new DirectoryInfo(path);
					var lm = new LISpMiner(directory, env);

					if (!env.Exists(lm.Id))
					{
						env.Register(lm);
					}
				}
				catch
				{
					continue;
				}
			}
		}

		protected void Application_Start()
		{
			AreaRegistration.RegisterAllAreas();

			SecurityConfig.ConfigureGlobal(GlobalConfiguration.Configuration);

			WebApiConfig.Register(GlobalConfiguration.Configuration);
			FilterConfig.RegisterGlobalFilters(GlobalFilters.Filters);
			RouteConfig.RegisterRoutes(RouteTable.Routes);

			// Register exisitng LMs
			RegisterExisting();

			// Load logging info
			log4net.Config.XmlConfigurator.Configure();

			ISessionManager sessionManager = new NHibernateSessionManager();
			sessionManager.Configuration.CurrentSessionContext<WebSessionContext>();

			SessionFactory = sessionManager.BuildSessionFactory();
		}

		protected void Application_Error(object sender, EventArgs e)
		{
			Log.Error(Server.GetLastError());
		}
	}
}