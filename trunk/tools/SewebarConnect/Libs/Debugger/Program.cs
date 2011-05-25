using System;
using LMWrapper.LISpMiner;
using LMWrapper.ODBC;

namespace Debugger
{
	class Program
	{
		static void Main(string[] args)
		{
			var env = new LMWrapper.Environment
			{
				LMPath = String.Format("{0}/../{1}", System.AppDomain.CurrentDomain.BaseDirectory, "LISp Miner"),
			};

			
			/*ODBCManagerRegistry.CreateDSN("Barbora",
										  "",
										  "Microsoft Access Driver (*.mdb)",
										  @"C:\Documents and Settings\Administrator\My Documents\svn\sewebar\trunk\tools\SewebarConnect\build\LISp Miner\Barbora.mdb");*/

			/*ODBCManagerRegistry.CreateDSN("Slot5",
			                              "",
										  "Microsoft Access Driver (*.mdb)",
			                              @"C:\Documents and Settings\Administrator\My Documents\svn\sewebar\trunk\tools\SewebarConnect\build\LISp Miner\LM Barbora.mdb");*/

			var exporter = new LMSwbExporter
			{
				LMPath = env.LMPath,
				Dsn = "LMEmpty2",
				//MatrixName = "Loans",
				Output = String.Format("{0}/results.xml", AppDomain.CurrentDomain.BaseDirectory),
				//Template = String.Format(@"{0}\Sewebar\Template\LMDataSource.Matrix.PMML.Template.txt", env.LMPath),
				Template = String.Format(@"{0}\Sewebar\Template\4ftMiner.Task.PMML.ARBuilder.Template.txt", env.LMPath),
				Alias = String.Format(@"{0}\Sewebar\Template\LM.PMML.Alias.txt", env.LMPath),
				NoProgress = false,
				Quiet = false,
				TaskName = "TaskM"
			};

			exporter.Launch();

			var importer = new LMSwbImporter
			               	{
			               		LMPath = env.LMPath,
			               		Dsn = "LMEmpty2",
								//Input = String.Format("{0}/main-task.pmml", AppDomain.CurrentDomain.BaseDirectory),
								Input = String.Format("{0}/barbora2_radek.pmml", AppDomain.CurrentDomain.BaseDirectory),
			               		//Quiet = true,
			               	};

			//importer.Run();

			Console.WriteLine("Done.");

			Console.Read();
		}
	}
}
