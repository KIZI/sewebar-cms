using System;
using System.IO;
using System.Linq;
using LMWrapper;
using LMWrapper.LISpMiner;

namespace SewebarConsole
{
	public class Program
	{
		public static void Main(string[] args)
		{
			string command = string.Empty;
			string module = string.Empty;
			string[] parameters = args.Skip(2).ToArray();

			if (args.Length == 1)
			{
				command = args[0].ToLower();
			}
			else if (args.Length > 1)
			{
				module = args[0].ToLower();
				command = args[1].ToLower();
			}

			switch (module)
			{
				case "lm":
					LM(command, parameters);
					break;
				case "key":
					ManageDatabase(command, parameters);
					break;
				default:
					Help();
					break;
			}

			Console.WriteLine();

			Console.WriteLine("Done.");
		}

		private static void Help()
		{
			Console.WriteLine("Usage: SewebarConsole [module] [command] [arguments]");
			Console.WriteLine();
			Console.WriteLine("modules:");
			Console.WriteLine("\tLM");
			Console.WriteLine("\t\tupdate [LM_LIB_PATH]");
			Console.WriteLine("\tKey");
			Console.WriteLine("\t\tcreate [nHibernate config]");
			Console.WriteLine("\t\tupdate [nHibernate config]");
			Console.WriteLine("\t\tinit [nHibernate config]");
			Console.WriteLine("\t\tmigrate [nHibernate configFrom] [nHibernate configTo]");
		}

		#region LM module 

		private static void LM(string command, string[] args)
		{
			switch (command)
			{
				case "update":
					Update(args.Length >= 1 ? args[0] : null);
					break;
				case "remove":
					Remove();
					break;
				default:
					Help();
					break;
			}
		}

		private static void Update(string lmLibPath)
		{
			try
			{
				var manager = new Manager(lmLibPath);

				manager.Update();
			}
			catch (Exception ex)
			{
				Console.WriteLine(ex.Message);
				Console.WriteLine();
				Console.WriteLine(ex.StackTrace);
			}
		}

		private static void Remove()
		{
			var env = new LMWrapper.Environment
			          	{
			          		//LMPoolPath = String.Format(@"{0}", @"C:\LMs\"),
			          		LMPoolPath = String.Format(@"{0}", @"c:\LMs"),
			          		LMPath = String.Format("{0}/../{1}", System.AppDomain.CurrentDomain.BaseDirectory, "LISp Miner"),
			          	};

			foreach (var path in Directory.GetDirectories(env.LMPoolPath))
			{
				try
				{
					var directory = new DirectoryInfo(path);
					var lm = new LISpMiner(directory, env);

					lm.Dispose();

					Console.WriteLine(lm.Id);
				}
				catch (Exception ex)
				{
					Console.WriteLine(String.Format("skipping {0} {1}", path, ex.Message));
				}
			}
		}

		#endregion

		#region Key module

		private static void ManageDatabase(string command, string[] args)
		{
			DatabaseManager databaseManager;

			if (args.Length > 0)
			{
				string cfg = args[0];
				
				if (!File.Exists(cfg))
				{
					cfg = Path.Combine(AppDomain.CurrentDomain.BaseDirectory, cfg);
					// normalize
					cfg = Path.GetFullPath((new Uri(cfg)).LocalPath);
				}
				
				databaseManager = new DatabaseManager(cfg);
			} 
			else
			{
				databaseManager = new DatabaseManager();
			}
			
			switch (command)
			{
				case "update":
					databaseManager.Update();
					break;
				case "create":
					databaseManager.Create();
					break;
				case "init":
					databaseManager.Init();
					break;
				case "migrate":
					if (args.Length > 1)
					{
						databaseManager.Migrate(args[1]);
					}
					else
					{
						Help();
					}
					break;
				default:
					Help();
					break;
			}
		}

		#endregion
	}
}
