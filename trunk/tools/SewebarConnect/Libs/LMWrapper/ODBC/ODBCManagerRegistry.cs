using System;
using System.Collections;
using System.Collections.Generic;
using Microsoft.Win32;

namespace LMWrapper.ODBC
{
	public enum DataSourceType { System, User }

	///<summary>
	/// Class to assist with creation and removal of ODBC DSN entries
	///</summary>
	public static class ODBCManagerRegistry
	{
		private const string ODBC_INI_REG_PATH = "SOFTWARE\\ODBC\\ODBC.INI\\";
		private const string ODBCINST_INI_REG_PATH = "SOFTWARE\\ODBC\\ODBCINST.INI\\";

		// Returns a list of data source names from the local machine.
		public static SortedList GetAllDataSourceNames()
		{
			// Get the list of user DSN's first.
			System.Collections.SortedList dsnList = GetUserDataSourceNames();

			// Get list of System DSN's and add them to the first list.
			System.Collections.SortedList systemDsnList = GetSystemDataSourceNames();
			for (int i = 0; i < systemDsnList.Count; i++)
			{
				string sName = systemDsnList.GetKey(i) as string;
				DataSourceType type = (DataSourceType)systemDsnList.GetByIndex(i);
				try
				{
					// This dsn to the master list
					dsnList.Add(sName, type);
				}
				catch
				{
					// An exception can be thrown if the key being added is a duplicate so 
					// we just catch it here and have to ignore it.
				}
			}

			return dsnList;
		}

		/// <summary>
		/// Gets all System data source names for the local machine.
		/// </summary>
		public static System.Collections.SortedList GetSystemDataSourceNames()
		{
			System.Collections.SortedList dsnList = new System.Collections.SortedList();

			// get system dsn's
			Microsoft.Win32.RegistryKey reg = (Microsoft.Win32.Registry.LocalMachine).OpenSubKey("Software");
			if (reg != null)
			{
				reg = reg.OpenSubKey("ODBC");
				if (reg != null)
				{
					reg = reg.OpenSubKey("ODBC.INI");
					if (reg != null)
					{
						reg = reg.OpenSubKey("ODBC Data Sources");
						if (reg != null)
						{
							// Get all DSN entries defined in DSN_LOC_IN_REGISTRY.
							foreach (string sName in reg.GetValueNames())
							{
								dsnList.Add(sName, DataSourceType.System);
							}
						}
						try
						{
							reg.Close();
						}
						catch { /* ignore this exception if we couldn't close */ }
					}
				}
			}

			return dsnList;
		}

		/// <summary>
		/// Gets all User data source names for the local machine.
		/// </summary>
		public static System.Collections.SortedList GetUserDataSourceNames()
		{
			System.Collections.SortedList dsnList = new System.Collections.SortedList();

			// get user dsn's
			Microsoft.Win32.RegistryKey reg = (Microsoft.Win32.Registry.CurrentUser).OpenSubKey("Software");
			if (reg != null)
			{
				reg = reg.OpenSubKey("ODBC");
				if (reg != null)
				{
					reg = reg.OpenSubKey("ODBC.INI");
					if (reg != null)
					{
						reg = reg.OpenSubKey("ODBC Data Sources");
						if (reg != null)
						{
							// Get all DSN entries defined in DSN_LOC_IN_REGISTRY.
							foreach (string sName in reg.GetValueNames())
							{
								dsnList.Add(sName, DataSourceType.User);
							}
						}
						try
						{
							reg.Close();
						}
						catch { /* ignore this exception if we couldn't close */ }
					}
				}
			}

			return dsnList;
		}

		/// <summary>
		/// Creates a new DSN entry with the specified values. If the DSN exists, the values are updated.
		/// </summary>
		/// <param name="dsnName">Name of the DSN for use by client applications</param>
		/// <param name="description">Description of the DSN that appears in the ODBC control panel applet</param>
		/// <param name="server">Network name or IP address of database server</param>
		/// <param name="driverName">Name of the driver to use</param>
		/// <param name="trustedConnection">True to use NT authentication, false to require applications to supply username/password in the connection string</param>
		/// <param name="database">Name of the datbase to connect to</param>
		public static void CreateDSN(string dsnName, string description, string server, string driverName, bool trustedConnection, string database)
		{
			// Lookup driver path from driver name
			var driverKey = Registry.LocalMachine.CreateSubKey(ODBCINST_INI_REG_PATH + driverName);
			if (driverKey == null) throw new Exception(string.Format("ODBC Registry key for driver '{0}' does not exist", driverName));
			string driverPath = driverKey.GetValue("Driver").ToString();

			// Add value to odbc data sources
			var datasourcesKey = Registry.LocalMachine.CreateSubKey(ODBC_INI_REG_PATH + "ODBC Data Sources");
			if (datasourcesKey == null) throw new Exception("ODBC Registry key for datasources does not exist");
			datasourcesKey.SetValue(dsnName, driverName);

			// Create new key in odbc.ini with dsn name and add values
			var dsnKey = Registry.LocalMachine.CreateSubKey(ODBC_INI_REG_PATH + dsnName);
			if (dsnKey == null) throw new Exception("ODBC Registry key for DSN was not created");
			dsnKey.SetValue("Database", database);
			dsnKey.SetValue("Description", description);
			dsnKey.SetValue("Driver", driverPath);
			dsnKey.SetValue("LastUser", System.Environment.UserName);
			dsnKey.SetValue("Server", server);
			dsnKey.SetValue("Database", database);
			dsnKey.SetValue("Trusted_Connection", trustedConnection ? "Yes" : "No");
		}

		/// <summary>
		/// Creates a new DSN entry with the specified values. If the DSN exists, the values are updated.
		/// </summary>
		/// <param name="dsnName">Name of the DSN for use by client applications</param>
		/// <param name="description">Description of the DSN that appears in the ODBC control panel applet</param>
		/// <param name="driverName">Name of the driver to use</param>
		/// <param name="database">Name of the datbase to connect to</param>
		public static void CreateDSN(string dsnName, string description, string driverName, string database)
		{
			// Lookup driver path from driver name
			var driverKey = Registry.LocalMachine.CreateSubKey(ODBCINST_INI_REG_PATH + driverName);
			if (driverKey == null) throw new Exception(string.Format("ODBC Registry key for driver '{0}' does not exist", driverName));
			string driverPath = driverKey.GetValue("Driver").ToString();

			// Add value to odbc data sources
			var datasourcesKey = Registry.LocalMachine.CreateSubKey(ODBC_INI_REG_PATH + "ODBC Data Sources");
			if (datasourcesKey == null) throw new Exception("ODBC Registry key for datasources does not exist");
			datasourcesKey.SetValue(dsnName, driverName);

			// Create new key in odbc.ini with dsn name and add values
			var dsnKey = Registry.LocalMachine.CreateSubKey(ODBC_INI_REG_PATH + dsnName);
			if (dsnKey == null) throw new Exception("ODBC Registry key for DSN was not created");
			dsnKey.SetValue("Driver", "Microsoft Access Driver (*.mdb)");
			dsnKey.SetValue("UID", System.Environment.UserName);
			dsnKey.SetValue("SafeTransactions", "0");
			//dsnKey.SetValue("ReadOnly", "0");
			//dsnKey.SetValue("MaxScanRows", "8");
			dsnKey.SetValue("FIL", "MS Access");
			dsnKey.SetValue("DriverId", "25");
			//dsnKey.SetValue("DefaultDir", );
			dsnKey.SetValue("DBQ", database);

			var dsnKeyJet = Registry.LocalMachine.CreateSubKey(ODBC_INI_REG_PATH + dsnName + "\\Engines\\Jet");
			if (dsnKeyJet == null) throw new Exception("ODBC Registry key for DSN was not created");
			dsnKeyJet.SetValue("ImplicitCommitSync", "Yes");
			dsnKeyJet.SetValue("MaxBufferSize", "2048");
			dsnKeyJet.SetValue("PageTimeout", "5");
			dsnKeyJet.SetValue("Threads", "3");
			dsnKeyJet.SetValue("UserCommitSync", "Yes");
		}

		/// <summary>
		/// Removes a DSN entry
		/// </summary>
		/// <param name="dsnName">Name of the DSN to remove.</param>
		public static void RemoveDSN(string dsnName)
		{
			// Remove DSN key
			Registry.LocalMachine.DeleteSubKeyTree(ODBC_INI_REG_PATH + dsnName);

			// Remove DSN name from values list in ODBC Data Sources key
			var datasourcesKey = Registry.LocalMachine.CreateSubKey(ODBC_INI_REG_PATH + "ODBC Data Sources");
			if (datasourcesKey == null) throw new Exception("ODBC Registry key for datasources does not exist");
			datasourcesKey.DeleteValue(dsnName);
		}

		///<summary>
		/// Checks the registry to see if a DSN exists with the specified name
		///</summary>
		///<param name="dsnName"></param>
		///<returns></returns>
		public static bool DSNExists(string dsnName)
		{
			var driversKey = Registry.LocalMachine.CreateSubKey(ODBCINST_INI_REG_PATH + "ODBC Drivers");
			if (driversKey == null) throw new Exception("ODBC Registry key for drivers does not exist");

			return driversKey.GetValue(dsnName) != null;
		}

		///<summary>
		/// Returns an array of driver names installed on the system
		///</summary>
		///<returns></returns>
		public static string[] GetInstalledDrivers()
		{
			var driversKey = Registry.LocalMachine.CreateSubKey(ODBCINST_INI_REG_PATH + "ODBC Drivers");
			if (driversKey == null) throw new Exception("ODBC Registry key for drivers does not exist");

			var driverNames = driversKey.GetValueNames();

			var ret = new List<string>();

			foreach (var driverName in driverNames)
			{
				if (driverName != "(Default)")
				{
					ret.Add(driverName);
				}
			}

			return ret.ToArray();
		}
	}
}
