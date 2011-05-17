using System;
using System.Collections.Generic;
using System.IO;
using System.Web;
using System.Web.Security;
using System.Web.SessionState;
using LMWrapper.ODBC;

namespace SewebarWeb
{
	public class Global : System.Web.HttpApplication
	{
		private static LMWrapper.Environment _env;

		public static LMWrapper.Environment Environment
		{
			get
			{
				if(_env == null)
				{
					_env = new LMWrapper.Environment
					{
						LMPath = String.Format(@"{0}\{1}", System.AppDomain.CurrentDomain.BaseDirectory, "LISp Miner"),
					};
				}

				return _env;
			}
		}

		protected void Application_Start(object sender, EventArgs e)
		{
			if (!ODBCManagerRegistry.DSNExists("Barbora"))
			{
				ODBCManagerRegistry.CreateDSN("Barbora",
											  "",
											  "Microsoft Access Driver (*.mdb)",
											  String.Format(@"{0}\db\Barbora.mdb", System.AppDomain.CurrentDomain.BaseDirectory));
			}
		}

		protected void Session_Start(object sender, EventArgs e)
		{
			var dsnName = String.Format("LM{0}", Session.SessionID);
			var metabaseOriginal = String.Format(@"{0}\db\LM Barbora.mdb", System.AppDomain.CurrentDomain.BaseDirectory);
			var metabase = String.Format(@"{0}\db\LM Barbora {1}.mdb", System.AppDomain.CurrentDomain.BaseDirectory,
			                             Session.SessionID);

			if (!ODBCManagerRegistry.DSNExists(dsnName) && !File.Exists(metabase))
			{
				ODBCManagerRegistry.CreateDSN(dsnName,
				                              "",
				                              "Microsoft Access Driver (*.mdb)",
				                              metabase);

				File.Copy(metabaseOriginal, metabase, true);
				Session["metabaseDsn"] = dsnName;
			}
		}

		protected void Application_BeginRequest(object sender, EventArgs e)
		{

		}

		protected void Application_AuthenticateRequest(object sender, EventArgs e)
		{

		}

		protected void Application_Error(object sender, EventArgs e)
		{

		}

		protected void Session_End(object sender, EventArgs e)
		{
			var dsnName = String.Format("LM Barbora-{0}", Session.SessionID);
			var metabase = String.Format(@"{0}\db\LM Barbora {1}.mdb", System.AppDomain.CurrentDomain.BaseDirectory,
										 Session.SessionID);

			if (ODBCManagerRegistry.DSNExists(dsnName))
			{
				ODBCManagerRegistry.RemoveDSN(dsnName);
			}

			File.Delete(metabase);
		}

		protected void Application_End(object sender, EventArgs e)
		{

		}
	}
}