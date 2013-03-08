using System;
using System.Runtime.Serialization;
using NHibernate.Cfg;
using NHibernate.Tool.hbm2ddl;

namespace SewebarKey.Configurations
{
	public class Manager
	{
		private static Configuration _cfg;

		public static Configuration Config
		{
			get
			{
				if (_cfg == null)
				{
					_cfg = new Configuration();
					_cfg.Configure(string.Format("{0}\\Configurations\\hibernate.cfg.xml", AppDomain.CurrentDomain.BaseDirectory));

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

		public Manager()
		{	
		}

		public void CreateDatabase()
		{
			var schemaExport = new SchemaExport(Config);
			schemaExport.Create(false, true);
		}
	}
}
