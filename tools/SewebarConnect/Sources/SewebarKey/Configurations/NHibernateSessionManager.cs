using System;
using NHibernate;
using NHibernate.Cfg;
using NHibernate.Tool.hbm2ddl;

namespace SewebarKey.Configurations
{
	public class NHibernateSessionManager : ISessionManager
	{
		private Configuration _cfg;

		public Configuration Configuration
		{
			get
			{
				if (_cfg == null)
				{
					_cfg = new Configuration();
					_cfg.Configure(string.Format("{0}\\hibernate.cfg.xml", AppDomain.CurrentDomain.BaseDirectory));

					//TODO: workaround pro threading SQLite. Problem pri uploadu souboru pres backgroundworkera a relativnim linkem na datasource. 
					if (_cfg.Properties["connection.driver_class"] == "NHibernate.Driver.SQLite20Driver" &&
						_cfg.Properties["connection.connection_string"] == "Data Source=data.sqlite")
					{
						_cfg.Properties["connection.connection_string"] = string.Format("Data Source={0}data.sqlite", AppDomain.CurrentDomain.BaseDirectory);
					}
				}

				return _cfg;
			}
		}

		public NHibernateSessionManager()
		{	
		}

		public void CreateDatabase()
		{
			var schemaExport = new SchemaExport(Configuration);
			schemaExport.Create(false, true);
		}

		public void UpdateDatabase()
		{
			var schemaExport = new SchemaUpdate(Configuration);
			schemaExport.Execute(false, true);
		}

		public ISessionFactory BuildSessionFactory()
		{
			return Configuration.BuildSessionFactory();
		}
	}
}
