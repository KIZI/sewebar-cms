using System;
using System.IO;
using LMWrapper;
using LMWrapper.LISpMiner;
using SewebarKey.Configurations;

namespace SewebarConsole
{
	public class Program
	{
		public static void Main(string[] args)
		{
			var command = args.Length > 0 ? args[0].ToLower() : string.Empty;

			switch (command)
			{
				case "update":
					Update(args.Length > 1 ? args[1] : null);
					break;
				case "remove":
					Remove();
					break;
				case "key":
					ManageDatabase();
					break;
				default:
					Help();
					break;
			}

			Console.WriteLine();

			Console.WriteLine("Done.");

			Console.Read();
		}

		private static void ManageDatabase()
		{
			ISessionManager databaseManager = new NHibernateSessionManager();

			databaseManager.CreateDatabase();
		}

		private static void Help()
		{
			Console.WriteLine("Possible commands:");
			Console.WriteLine();
			Console.WriteLine("\tupdate LM_LIB_PATH");
			// Console.WriteLine("\tremove");
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
	}
}
